<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class GameRoundsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('game_rounds')->insert([
            'round' => 1,
            'winner' => 'none',
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
