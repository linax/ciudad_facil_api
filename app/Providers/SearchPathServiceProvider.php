<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;

class SearchPathServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        DB::statement('SET search_path TO '.config('database.connections.pgsql.search_path'));
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
