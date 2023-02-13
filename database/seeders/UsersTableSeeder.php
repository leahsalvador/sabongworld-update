<?php
namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Nordave Bontigao',
            'email' => 'nordaveb@gmail.com',
            'username' => 'admin',
            'phone_number' => 639324707947,
            'code' => '5ba9f0421780208bad390d57334a89744f554434',
            'user_level' => 'admin',
            'facebook_link' => 'Nordave Bontigao',
            'email_verified_at' => now(),
            'password' => Hash::make('ngothailand'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('users')->insert([
            'name' => 'Bot Agent',
            'email' => 'botagent@gmail.com',
            'username' => 'botagent',
            'phone_number' => 639123456789,
            'code' => '5ba9f0421780208ba2ju3u423jk4g234gs',
            'referral_id' => '5ba9f0421780208bad390d57334a89744f554434',
            'user_level' => 'master-agent',
            'email_verified_at' => now(),
            'password' => Hash::make('secret'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('users')->insert([
            'name' => 'Bot Player 1',
            'email' => 'botplayer1@gmail.com',
            'username' => 'botplayer1',
            'phone_number' => 639234567890,
            'referral_id' => '5ba9f0421780208bad390d57334a89744f554434',
            'user_level' => 'master-agent-player',
            'email_verified_at' => now(),
            'password' => Hash::make('secret'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        DB::table('users')->insert([
            'name' => 'Bot Player 2',
            'email' => 'botplayer2@gmail.com',
            'username' => 'botplayer2',
            'phone_number' => 639345678901,
            'referral_id' => '5ba9f0421780208bad390d57334a89744f554434',
            'user_level' => 'master-agent-player',
            'email_verified_at' => now(),
            'password' => Hash::make('secret'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        DB::table('users')->insert([
            'name' => 'Bot Player 3',
            'email' => 'botplayer3@gmail.com',
            'username' => 'botplayer3',
            'phone_number' => 639456789012,
            'referral_id' => '5ba9f0421780208bad390d57334a89744f554434',
            'user_level' => 'master-agent-player',
            'email_verified_at' => now(),
            'password' => Hash::make('secret'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        DB::table('users')->insert([
            'name' => 'Bot Player 4',
            'email' => 'botplayer4@gmail.com',
            'username' => 'botplayer4',
            'phone_number' => 639567890123,
            'referral_id' => '5ba9f0421780208bad390d57334a89744f554434',
            'user_level' => 'master-agent-player',
            'email_verified_at' => now(),
            'password' => Hash::make('secret'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        DB::table('users')->insert([
            'name' => 'Bot Player 5',
            'email' => 'botplayer5@gmail.com',
            'username' => 'botplayer5',
            'phone_number' => 639678901234,
            'referral_id' => '5ba9f0421780208bad390d57334a89744f554434',
            'user_level' => 'master-agent-player',
            'email_verified_at' => now(),
            'password' => Hash::make('secret'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
}
}
