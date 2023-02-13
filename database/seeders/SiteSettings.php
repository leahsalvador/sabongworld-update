<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SiteSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('site_settings')->insert([
            'name' => 'Game Result Controller',
            'value' => 'ON',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('site_settings')->insert([
            'name' => 'Game Announcement',
            'value' => 'Game starts at 8:00 am to 6:00 am Daily',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('site_settings')->insert([
            'name' => 'Bot Minimum Bet',
            'value' => '55000',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('site_settings')->insert([
            'name' => 'Bot Maximum Bet',
            'value' => '60000',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
