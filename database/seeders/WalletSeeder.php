<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('wallets')->insert([
            'user_id' => 1,
            'points' => 999999,
            'comission' => 500,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('wallets')->insert([
            'user_id' => 2,
            'points' => 999999,
            'comission' => 500,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('wallets')->insert([
            'user_id' => 3,
            'points' => 0,
            'comission' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('wallets')->insert([
            'user_id' => 4,
            'points' => 0,
            'comission' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('wallets')->insert([
            'user_id' => 5,
            'points' => 0,
            'comission' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('wallets')->insert([
            'user_id' => 6,
            'points' => 0,
            'comission' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('wallets')->insert([
            'user_id' => 7,
            'points' => 0,
            'comission' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('wallets')->insert([
            'user_id' => 8,
            'points' => 0,
            'comission' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('wallets')->insert([
            'user_id' => 9,
            'points' => 0,
            'comission' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('wallets')->insert([
            'user_id' => 10,
            'points' => 0,
            'comission' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('wallets')->insert([
            'user_id' => 11,
            'points' => 0,
            'comission' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('wallets')->insert([
            'user_id' => 12,
            'points' => 0,
            'comission' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
     
    }
}
