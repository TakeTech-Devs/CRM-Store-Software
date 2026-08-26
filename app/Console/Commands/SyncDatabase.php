<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncDatabase extends Command
{
    protected $signature = 'db:sync';
    protected $description = 'Sync all tables from local to hosted database';

    public function handle()
    {
        $this->info('Starting database sync...');

        // Get all table names from the local database
        $tables = $this->getAllTables();

        foreach ($tables as $table) {
            try {
                // Sync logic for each table
                $lastRecord = DB::connection('hosted')->table($table)->latest('id')->first();
                $lastId = $lastRecord ? $lastRecord->id : 0;

                $newData = DB::table($table)->where('id', '>', $lastId)->get();

                if ($newData->isNotEmpty()) {
                    DB::connection('hosted')->table($table)->insert($newData->toArray());
                    $this->info("Successfully synced {$newData->count()} records for table: {$table}");
                } else {
                    $this->info("No new data to sync for table: {$table}");
                }
            } catch (\Exception $e) {
                $this->error("Error syncing table {$table}: " . $e->getMessage());
            }
        }

        $this->info('Database sync completed!');
    }


    private function getAllTables()
    {
        $tables = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_' . env('DB_DATABASE');

        return collect($tables)->pluck($tableKey)->toArray();
    }
}
