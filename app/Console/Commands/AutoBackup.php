<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackup;
use Illuminate\Console\Command;

class AutoBackup extends Command
{
    protected $signature = 'backup:auto';
    protected $description = 'Take an automatic database backup and delete automatic backups older than 30 days';

    public function handle(DatabaseBackup $backup): int
    {
        try {
            $result = $backup->run(DatabaseBackup::AUTO_PREFIX);
            $this->info('Backup created: ' . $result['file']);
            $backup->pruneAutoBackups();
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        } finally {
            $backup->releaseLock();
        }
    }
}
