<?php

namespace App\Listeners;

use App\Events\StoreCreated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateStoreDatabase
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(StoreCreated $event)
    {
        $store=$event->store;
        $db='tenancy_store_'.$store->id;
        $store->database_options=[
            'dbname'=>$db,
        ];
        $store->save();

        DB::statement("CREATE DATABASE `{$db}`");
        Config::set('database.connections.tenant.database',$db);
        Artisan::call('migrate',[
            '--path'=>'database/migrations/tenants',
            '--database'=>'tenant',
            // '--force'=>true,
        ]);
        

    }
}
