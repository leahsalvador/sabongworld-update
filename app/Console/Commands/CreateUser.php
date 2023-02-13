<?php

namespace App\Console\Commands;

use App\Models\Registration;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Hash;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

use Faker\Factory as Faker;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create
                            {username : The login username of the user}
                            {type=4 : User type as integer 0-5}
                            {password=12345678 : User password for login}
                            {points=0 : Deposit points to user wallate }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create user from command line. User type are
                                [Super Admin, Admin, Master Agent, Sub Agent, Master agent player, sub agent player].
                                 Argument: username type password points' ;

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
        $faker = Faker::create();
        $types = ['super-admin', 'admin', 'master-agent', 'sub-agent','master-agent-player', 'sub-agent-player','gold-agent','gold-agent-player','silver-agent','silver-agent-player','bronze-agent','bronze-agent-player'];
        $userName = $this->argument('username');
        $password = $this->argument('password');
        $type = $this->argument('type');
        $points = $this->argument('points');
        $user_id = "";
        $data = [
            'name' => $faker->name,
            'email' => $faker->safeEmail,
            'username' => $userName,
            'phone_number' => $faker->e164PhoneNumber,
            'code' => Str::random(40),
            'user_level' => $types[$type],
            'email_verified_at' => now(),
            'password' => Hash::make($password),
            'created_at' => now(),
            'updated_at' => now()
        ];
        switch ($type) {
            case '2':
                $reff = $this->getUser('admin');
                $data['referral_id'] = $reff[0]['code'];
                break;
            case '3':
            case '4':
                $reff = $this->getUser('master-agent');
                $data['referral_id'] = $reff[0]['code'];
                break;
            case '5':
            case '6':
                $reff = $this->getUser('sub-agent');
                $data['referral_id'] = $reff[0]['code'];
                break;
            case '7':
            case '8':
                $reff = $this->getUser('gold-agent');
                $data['referral_id'] = $reff[0]['code'];
                break;
            case '9':
            case '10':
                $reff = $this->getUser('silver-agent');
                $data['referral_id'] = $reff[0]['code'];
                break;
            case '11':
                $reff = $this->getUser('bronze-agent');
                $data['referral_id'] = $reff[0]['code'];
                break;
        }

        $user = User::create($data);
        if ($user) {
            Wallet::create([
                "user_id" => $user->id,
                "points" => $points,
                "comission" => "0.00"
            ]);
            $this->info("{$types[$type]} User {$userName} create successful!");
            $data['deleted_at'] = now();
            $reg = Registration::create($data);
        }
        return $user->id;
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
        User::select('id', 'username')->where([
            ['user_level', '=', $userType],
            ['status', '=', 'activated']
        ])->get()->map(function ($item) use (&$users) {
            $users[$item['id']] = $item['username'];
        });
        $user_id = array_search($this->choice(
            $text,
            $users
        ), $users);
        return User::select('code')->where('id', $user_id)->get();
    }
}
