<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;
use ZipArchive;

class UpdateController extends Controller
{
    private const UPDATED_FOLDERS = ['app', 'resources', 'routes', 'database'];
    private const MAX_ATTEMPTS = 3;

    public function checkForUpdate()
    {
        try {
            $manifest = $this->fetchManifest();
            $currentVersion = $this->currentVersion();

            return response()->json([
                'status' => 200,
                'data' => [
                    'current_version' => $currentVersion,
                    'latest_version' => $manifest['version'],
                    'has_update' => version_compare($manifest['version'], $currentVersion, '>'),
                ],
            ]);
        } catch (Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()], 500);
        }
    }

    public function applyUpdate()
    {
        try {
            $manifest = $this->fetchManifest();
            $currentVersion = $this->currentVersion();

            if (!version_compare($manifest['version'], $currentVersion, '>')) {
                return response()->json(['status' => 200, 'message' => 'Already up to date.']);
            }
        } catch (Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()], 500);
        }

        $logId = DB::table('update_log')->insertGetId([
            'previous_version' => $currentVersion,
            'new_version' => $manifest['version'],
            'status' => 'pending',
            'attempt_count' => 0,
            'started_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $tempZip = storage_path('app' . DIRECTORY_SEPARATOR . 'update-' . $manifest['version'] . '.zip');
        $tempExtractDir = storage_path('app' . DIRECTORY_SEPARATOR . 'update-extract-' . $manifest['version']);

        $lastError = null;
        $success = false;

        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; $attempt++) {
            DB::table('update_log')->where('id', $logId)->update([
                'attempt_count' => $attempt,
                'updated_at' => now(),
            ]);

            try {
                $this->cleanupTemp($tempZip, $tempExtractDir);

                $this->downloadZip($manifest['download_url'], $tempZip);
                $this->extractZip($tempZip, $tempExtractDir);
                $changedFiles = $this->applyExtractedFolders($tempExtractDir);

                Artisan::call('migrate', ['--force' => true]);
                $migrationsOutput = trim(Artisan::output());
                Artisan::call('route:clear');
                Artisan::call('view:clear');

                DB::table('update_log')->where('id', $logId)->update([
                    'status' => 'success',
                    'changed_files' => json_encode($changedFiles),
                    'migrations_run' => json_encode($migrationsOutput !== '' ? explode("\n", $migrationsOutput) : []),
                    'completed_at' => now(),
                    'updated_at' => now(),
                ]);

                $success = true;
                break;
            } catch (Throwable $th) {
                $lastError = $th->getMessage();
            } finally {
                $this->cleanupTemp($tempZip, $tempExtractDir);
            }
        }

        if (!$success) {
            DB::table('update_log')->where('id', $logId)->update([
                'status' => 'failed',
                'error_message' => $lastError,
                'completed_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'status' => 500,
                'message' => 'Update failed after ' . self::MAX_ATTEMPTS . ' attempts: ' . $lastError,
            ], 500);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Update applied successfully.',
            'data' => ['version' => $manifest['version']],
        ]);
    }

    public function history()
    {
        $logs = DB::table('update_log')->orderByDesc('id')->get();

        return response()->json(['status' => 200, 'data' => $logs]);
    }

    private function currentVersion(): string
    {
        return DB::table('update_log')
            ->where('status', 'success')
            ->orderByDesc('id')
            ->value('new_version') ?? '1.0.0';
    }

    private function fetchManifest(): array
    {
        $manifestUrl = config('update.manifest_url');
        if (!$manifestUrl) {
            throw new \Exception('Update server is not configured.');
        }

        $response = Http::timeout(10)->get($manifestUrl);
        if (!$response->successful()) {
            throw new \Exception('Could not reach the update server.');
        }

        $manifest = $response->json();
        if (empty($manifest['version']) || empty($manifest['download_url'])) {
            throw new \Exception('Update server returned an invalid manifest.');
        }

        return $manifest;
    }

    private function downloadZip(string $url, string $destination): void
    {
        $response = Http::timeout(300)->get($url);
        if (!$response->successful()) {
            throw new \Exception('Failed to download update package (HTTP ' . $response->status() . ').');
        }
        file_put_contents($destination, $response->body());
    }

    private function extractZip(string $zipPath, string $extractTo): void
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new \Exception('Could not open the downloaded update package.');
        }
        mkdir($extractTo, 0777, true);
        $zip->extractTo($extractTo);
        $zip->close();
    }

    // Only ever copies the four allowed folders — anything else present in the
    // zip is ignored, even if the uploaded package contained it by mistake.
    private function applyExtractedFolders(string $extractDir): array
    {
        $changedFiles = [];
        foreach (self::UPDATED_FOLDERS as $folder) {
            $source = $extractDir . DIRECTORY_SEPARATOR . $folder;
            if (is_dir($source)) {
                $changedFiles = array_merge($changedFiles, $this->copyDirectory($source, base_path($folder)));
            }
        }

        return $changedFiles;
    }

    private function copyDirectory(string $source, string $destination): array
    {
        $changed = [];

        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($items as $item) {
            $relativePath = substr($item->getPathname(), strlen($source) + 1);
            $target = $destination . DIRECTORY_SEPARATOR . $relativePath;

            if ($item->isDir()) {
                if (!is_dir($target)) {
                    mkdir($target, 0777, true);
                }
            } else {
                copy($item->getPathname(), $target);
                $changed[] = $relativePath;
            }
        }

        return $changed;
    }

    private function cleanupTemp(string $zipPath, string $extractDir): void
    {
        if (file_exists($zipPath)) {
            unlink($zipPath);
        }
        if (is_dir($extractDir)) {
            $this->deleteDirectory($extractDir);
        }
    }

    private function deleteDirectory(string $dir): void
    {
        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($dir);
    }
}
