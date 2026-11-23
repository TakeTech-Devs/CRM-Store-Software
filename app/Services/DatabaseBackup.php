<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

/**
 * Database backups (mysqldump) for the store, shared by the manual "Generate
 * Backup" button and the automatic three-a-day backup.
 */
class DatabaseBackup
{
    /** Automatic backups are due once each of these (store-local) times has passed. */
    public const SLOTS = ['10:00', '14:00', '18:00'];

    /** The store runs on Indian time, whatever the app's own timezone is. */
    private const SLOT_TIMEZONE = 'Asia/Kolkata';

    /** Automatic backups older than this are deleted so the disk does not fill up. */
    private const KEEP_AUTO_DAYS = 30;

    /** A lock older than this is assumed to belong to a crashed run. */
    private const LOCK_STALE_SECONDS = 900;

    public const AUTO_PREFIX = 'auto-backup';

    private function backupDir(): string
    {
        return storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'backup');
    }

    private function lockFile(): string
    {
        return storage_path('app' . DIRECTORY_SEPARATOR . 'auto-backup.lock');
    }

    /**
     * Dump the database to storage/app/public/backup and record it in the `backup` table.
     *
     * @return array{file: string, url: string}
     * @throws RuntimeException with a message safe to show to the user's logs
     */
    public function run(string $prefix = 'backup'): array
    {
        // config(), not env(): RightAid.bat runs config:cache, after which env() returns null.
        $cfg = config('database.connections.' . config('database.default'));

        $dir = $this->backupDir();
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $fileName = $prefix . '-' . date('Y-m-d_H-i-s') . '.sql';
        $filePath = $dir . DIRECTORY_SEPARATOR . $fileName;

        $dump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
        if (!is_file($dump)) {
            $dump = 'mysqldump';
        }

        // --single-transaction: a consistent snapshot without locking tables, so
        // billing carries on normally while a backup runs.
        $command = '"' . $dump . '"'
            . ' --single-transaction'
            . ' --user=' . escapeshellarg(trim((string) $cfg['username']))
            . (trim((string) $cfg['password']) !== '' ? ' --password=' . escapeshellarg(trim((string) $cfg['password'])) : '')
            . ' --host=' . escapeshellarg(trim((string) $cfg['host']))
            . ' --port=' . (int) $cfg['port']
            . ' ' . escapeshellarg(trim((string) $cfg['database']))
            . ' > "' . $filePath . '"';

        $process = proc_open($command, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        if (!is_resource($process)) {
            throw new RuntimeException('Could not start mysqldump.');
        }
        fclose($pipes[0]);
        stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $exit = proc_close($process);

        if ($exit !== 0 || !is_file($filePath) || filesize($filePath) === 0) {
            if (is_file($filePath)) {
                @unlink($filePath);
            }
            throw new RuntimeException('Backup failed: ' . trim($error ?: 'mysqldump produced no output'));
        }

        // Host-independent link, so it keeps working whatever address/port the app is opened on.
        $relative = 'storage/backup/' . $fileName;
        DB::table('backup')->insert([
            'date'      => date('Y-m-d H:i:s'),
            'file_path' => '/' . $relative,
            'file_name' => $fileName,
        ]);

        return ['file' => $fileName, 'url' => url($relative)];
    }

    /** The most recent slot time that has already passed today, or null before the first one. */
    private function latestPassedSlot(): ?Carbon
    {
        $now = Carbon::now(self::SLOT_TIMEZONE);
        $passed = null;
        foreach (self::SLOTS as $slot) {
            [$h, $m] = array_map('intval', explode(':', $slot));
            $at = $now->copy()->setTime($h, $m, 0);
            if ($at->lte($now) && (!$passed || $at->gt($passed))) {
                $passed = $at;
            }
        }
        return $passed;
    }

    /** True when a slot has passed since the last automatic backup. */
    public function autoBackupDue(): bool
    {
        $slot = $this->latestPassedSlot();
        if (!$slot) {
            return false;
        }

        $last = DB::table('backup')
            ->where('file_name', 'like', self::AUTO_PREFIX . '-%')
            ->orderByDesc('id')
            ->value('date');

        return !$last || Carbon::parse($last)->lt($slot);
    }

    /**
     * Start an automatic backup in a separate, detached process and return at once.
     * (The store server handles one request at a time, so dumping inside the
     * request would freeze the app until it finished.)
     */
    public function startAutoBackup(): bool
    {
        $lock = $this->lockFile();
        if (is_file($lock) && (time() - filemtime($lock)) < self::LOCK_STALE_SECONDS) {
            return false; // another backup is already running
        }
        file_put_contents($lock, (string) time());

        $php = (stripos(basename(PHP_BINARY), 'php') === 0) ? PHP_BINARY : 'php';
        $artisan = base_path('artisan');

        if (DIRECTORY_SEPARATOR === '\\') {
            $cmd = 'start "" /B "' . $php . '" "' . $artisan . '" backup:auto > NUL 2>&1';
        } else {
            $cmd = '"' . $php . '" "' . $artisan . '" backup:auto > /dev/null 2>&1 &';
        }

        $handle = @popen($cmd, 'r');
        if ($handle === false) {
            @unlink($lock);
            return false;
        }
        pclose($handle);
        return true;
    }

    public function releaseLock(): void
    {
        @unlink($this->lockFile());
    }

    /** Delete automatic backups older than the retention period (manual backups are never touched). */
    public function pruneAutoBackups(): int
    {
        $cutoff = Carbon::now()->subDays(self::KEEP_AUTO_DAYS)->format('Y-m-d H:i:s');
        $old = DB::table('backup')
            ->where('file_name', 'like', self::AUTO_PREFIX . '-%')
            ->where('date', '<', $cutoff)
            ->get();

        foreach ($old as $row) {
            File::delete($this->backupDir() . DIRECTORY_SEPARATOR . $row->file_name);
            DB::table('backup')->where('id', $row->id)->delete();
        }
        return $old->count();
    }
}
