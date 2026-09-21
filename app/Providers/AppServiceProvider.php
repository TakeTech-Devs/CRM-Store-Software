<?php

namespace App\Providers;

use Illuminate\Database\Events\MigrationsStarted;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Older MySQL/MariaDB (before 5.7.7 / 10.2.2, or tables in the old COMPACT row
        // format) cap an index key at 767 bytes, but a utf8mb4 VARCHAR(255) key needs
        // 1020, so building the schema fails with error 1071/1709 (sessions, the unique
        // business keys, ...). When a migration run starts on such a server, shorten the
        // default string column to 191 (764 bytes) so the schema still builds. Modern
        // servers stay at 255, keeping every store's schema identical to the admin's.
        Event::listen(MigrationsStarted::class, function () {
            if ($this->serverRejectsLongIndexKeys()) {
                Schema::defaultStringLength(191);
            }
        });
    }

    /**
     * Asks the server directly instead of guessing from its version number: creates
     * a throwaway table with a full-length utf8mb4 unique key, exactly like the
     * real migrations do, and sees whether the server accepts it.
     */
    private function serverRejectsLongIndexKeys(): bool
    {
        try {
            DB::statement('CREATE TEMPORARY TABLE rightaid_key_probe (v VARCHAR(255) NOT NULL, UNIQUE KEY (v)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
            DB::statement('DROP TEMPORARY TABLE rightaid_key_probe');

            return false;
        } catch (QueryException $e) {
            // 1071 = key too long (MySQL / older MariaDB), 1709 = index column too large.
            // Any other failure (e.g. no CREATE TEMPORARY privilege) isn't a key-length
            // problem, so leave the default alone rather than guess.
            return in_array($e->errorInfo[1] ?? null, [1071, 1709], true);
        }
    }
}
