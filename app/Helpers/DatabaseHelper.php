<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('getAllTables')) {
    function getAllTables()
    {
        $tables = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_' . env('DB_DATABASE');

        return collect($tables)->pluck($tableKey)->toArray();
    }
}
