<?php

namespace App\Console\Commands;

use App\Models\Registration;
use Faker\Factory as Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateBotUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-bot {count} {agent-id?} {type=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will create number of bot user account automatically.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user = false;
        $referral_id = "";
        $faker = Faker::create();
        $count = $this->argument('count');
        $type = $this->argument('type');
        $agentId = $this->argument('agent-id');
        $types = ['master-agent-player', 'sub-agent-player'];
        if (is_null($agentId)) {
            switch ($type) {
                case '0':
                    $user = $this->getUser('master-agent');
                    break;
                case '1':
                    $user = $this->getUser('sub-agent');
                    break;
            }
        } else {
            $user = DB::table('users')->select('code')->where('id', $agentId)->get()->get(0);
        }
        $users = [];
        $usrs = DB::table('users')->select('id')->orderByDesc('id')->first();
        for ($i = 0; $i < $count; $i++) {
            $data = [
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'username' => "bot" . ($usrs->id + $i),
                'phone_number' => $faker->e164PhoneNumber,
                'code' => Str::random(40),
                'user_level' => $types[$type],
                'email_verified_at' => now(),
                'password' => Hash::make('Bot@123.com'),
                'referral_id' => $user->code,
                'created_at' => now(),
                'updated_at' => now()
            ];
            $uid = DB::table('users')->insertGetId($data);
            if ($uid) {
                $wallet = DB::table('wallets')->insert([
                    "user_id" => $uid,
                    "points" => 0,
                    "comission" => "0.00"
                ]);
                $this->info("User {$uid} create successful!");
                $data['deleted_at'] = now();
                $reg = Registration::create($data);
            }
            array_push($users, $uid);
        }
        echo $ids = join(',', $users);
        return $ids;
    }

    private function getUser($userType)
    {
        $users = array();
        $text = "Select User.....";
        switch ($userType) {
            case 'master-agent':
                $text = "Who is your Admin?";
                break;
            case 'sub-agent':
            case 'master-agent-player':
            case 'sub-agent-player':
                $text = "Who is your Agent?";
                break;
        }
        DB::table('users')->select('id', 'username')->where([
            ['user_level', '=', $userType],
            ['status', '=', 'activated']
        ])->get()->map(function ($item) use (&$users) {
            $users[$item->id] = $item->username;
        });
        $user_id = array_search($this->choice(
            $text,
            $users
        ), $users);
        return DB::table('users')->select('code')->where('id', $user_id)->get()->get(0);
    }
}
