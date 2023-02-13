<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\PaymentMethod;
use App\Models\BettingHistory;
use App\Models\GameRound;
use App\Models\SiteSettings;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function admin()
    {
        return view('dashboard');
    }

    public function superAdmin()
    {
        $data = ['income' => []];
        return view('/superAdmin/home', $data);
    }

    public function player()
    {
        $user = Auth::user();
        $betting_histories = $user->bettingHistory;
        $game_rounds = GameRound::whereIn('status', ['upcoming', 'open', 'ongoing', 'final-bet', 'cancelled', 'closed', 'draw'])
            ->whereRaw('created_at >= CURRENT_DATE')
            ->orderBy('round', 'asc')->get();

        $game_rounds_current = GameRound::where('status', 'open')
            ->whereRaw('created_at >= CURRENT_DATE')
            ->orderBy('round', 'asc')
            ->first();

        $current_id = 0;
        if ($game_rounds_current) {
            $heads = $user->totalHeadBet($game_rounds_current->id)->sum('amount');
            $tails = $user->totalTailsBet($game_rounds_current->id)->sum('amount');
            $current_id = $game_rounds_current->id;
        } else {
            $heads = 0;
            $tails = 0;
        }

        $announcement = SiteSettings::where('id', 2)->pluck('value')->first();
        $betting_histories_current = ['heads' => $heads, 'tails' => $tails];
        $videoSetting = SiteSettings::where('name', 'LIKE', '%video%')->orWhere('name', 'LIKE', '%Video%')->first();

        $data = [
            'player' => $user,
            'betting_histories' => $betting_histories,
            'game_rounds' => $game_rounds,
            'game_rounds_current' => $game_rounds_current,
            'betting_histories_current' => $betting_histories_current,
            'announcement' => $announcement,
            'name' => 'Tish',
            'current' => $current_id,
            'video_setting' => $videoSetting,
            'minimum_bet' => config('settings.minimum_bet'),
            'no_video_point' => config('settings.no_video_point'),
        ];
        $siteSettings_url = SiteSettings::where('id', 10)->first();
        $data['video_url'] = $siteSettings_url->value;

        return view('player.home', $data);
    }

    public function game_details_bet(Request $request)
    {
        $user = Auth::user();
        $game_rounds_current = GameRound::orderBy('round', 'asc')->first();
        $betting_histories = $user->bettingHistory;
        if ($game_rounds_current) {

            $old_head_bet = $game_rounds_current->total_bet_heads;
            $old_tails_bet = $game_rounds_current->total_bet_tails;

            $head_bet = $game_rounds_current->total_bet_heads;
            $tails_bet = $game_rounds_current->total_bet_tails;
            $winner = $game_rounds_current->winner;
            $attempts = 1;
            $game_status = $game_rounds_current->status;

            if ($game_status == 'open') {
                while ($game_status == 'open' && $attempts <= 50) {
                    sleep(1);
                    $game_status = $game_rounds_current->refresh()->status;
                    $head_bet = $game_rounds_current->refresh()->total_bet_heads;
                    $tails_bet = $game_rounds_current->refresh()->total_bet_tails;
                    $winner = $game_rounds_current->refresh()->winner;
                    $attempts++;
                }
            } else if ($game_status == 'final-bet') {
                while ($game_status == 'final-bet' && $attempts <= 50) {
                    sleep(1);
                    $game_status = $game_rounds_current->refresh()->status;
                    $head_bet = $game_rounds_current->refresh()->total_bet_heads;
                    $tails_bet = $game_rounds_current->refresh()->total_bet_tails;
                    $winner = $game_rounds_current->refresh()->winner;
                    $attempts++;
                }
            } else {
                while ($game_status == 'closed' && $attempts <= 50) {
                    sleep(1);
                    $game_status = $game_rounds_current->refresh()->status;
                    $head_bet = $game_rounds_current->refresh()->total_bet_heads;
                    $tails_bet = $game_rounds_current->refresh()->total_bet_tails;
                    $winner = $game_rounds_current->refresh()->winner;
                    $attempts++;
                }
            }
            $heads = $user->totalHeadBet($game_rounds_current->id)->sum('amount');
            $tails = $user->totalTailsBet($game_rounds_current->id)->sum('amount');
            $win_payout_tails = $betting_histories->where('game_rounds_id', $game_rounds_current->id)->where('status', 'win')->where('side', 'tails')->pluck('win_amount')->first();
            $win_payout_heads = $betting_histories->where('game_rounds_id', $game_rounds_current->id)->where('status', 'win')->where('side', 'heads')->pluck('win_amount')->first();
        } else {
            $heads = 0;
            $tails = 0;
            $win_payout_tails = 0;
            $win_payout_heads = 0;
        }
        $heads_data = ['payout' => $game_rounds_current->head_payout, 'total_bet' => $head_bet, 'current_bet' => $heads, 'win' => $win_payout_heads ?? 0];
        $tails_data = ['payout' => $game_rounds_current->tails_payout, 'total_bet' => $tails_bet, 'current_bet' => $tails, 'win' => $win_payout_tails ?? 0];
        $data = [
            'head' => $heads_data, 'tails' => $tails_data, 'status' => $game_status, 'round' => $game_rounds_current->round, 'id' => $game_rounds_current->id, 'coin1' => $game_rounds_current->coin1, 'coin2' => $game_rounds_current->coin2, 'winner' => $game_rounds_current->winner, 'current_wallet_points' => $user->wallet->points
        ];
        return response()->json($data);
    }


    public function agent()
    {
        $user = Auth::user();
        $com = 0;
        switch ($user->user_level) {
            case 'super-admin':
                $com = (config('settings.commission.superadmin') * 100);
                break;
            case 'admin':
                $com = (config('settings.commission.admin') * 100);
                break;
            case 'master-agent':
                $com = (config('settings.commission.master') * 100);
                break;
            case 'sub-agent':
                $com = (config('settings.commission.sub') * 100);
                break;
            case 'gold-agent':
                $com = (config('settings.commission.gold') * 100);
                break;
            case 'silver-agent':
                $com = (config('settings.commission.silver') * 100);
                break;
            case 'bronze-agent':
                $com = (config('settings.commission.bronze') * 100);
                break;

        }
        $data = [
            'player' => $user,
            'commission' => ($user->commission_percent > 0) ? $user->commission_percent : $com,
        ];

        return view('agent.home', $data);
    }

    public function test()
    {
        return $game_rounds = GameRound::orderBy('round', 'asc')->get();
    }

    public function isOpen()
    {
        $res = 0;
        try {
            $all_game = GameRound::whereIn('status', ['open', 'upcoming'])->whereDate('created_at', now())->count();
            if ($all_game > 0) {
                $res = 1;
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
        return $res;
    }

    public function logs()
    {
        $user = Auth::user();
        $code = $user->code;
        $agent_players = User::where('referral_id', $code)->get();
        $player_ids = [];
        foreach ($agent_players as $player) {
            array_push($player_ids, $player->id);
        }
        $betting_histories = $user->bettingHistory;
        $game_rounds = GameRound::orderBy('created_at', 'desc')->get();
        // $game_rounds_current = GameRound::whereDay('play_date', '=', date('d'))->whereIn('status',['upcoming','open','ongoing'])->first();
        $game_rounds_current = GameRound::whereIn('status', ['upcoming', 'open', 'ongoing'])->first();
        if ($game_rounds_current) {
            $heads = $user->totalHeadBet($game_rounds_current->id)->sum('amount');
            $tails = $user->totalTailsBet($game_rounds_current->id)->sum('amount');
        } else {
            $heads = [];
            $tails = [];
        }
        if ($user->user_level != 'admin') {
            $players_playings = BettingHistory::whereIn('player_id', $player_ids)->get();
        } else {
            $players_playings = BettingHistory::whereIn('status', ['win', 'loose', 'undo', 'cancelled', 'ongoing'])->orderBy('id', 'DESC')->get();
        }
        $betting_histories_current = ['heads' => $heads, 'tails' => $tails];
        $data = [
            'player' => $user,
            'betting_histories' => $betting_histories,
            'game_rounds' => $game_rounds,
            'game_rounds_current' => $game_rounds_current,
            'betting_histories_current' => $betting_histories_current,
            'players_playings' => $players_playings,

        ];
        return view('agent.logs', $data);
    }

    /**
     * This method will render route list view
     **/
    public function routes()
    {
        \Artisan::call('route:list');
        return '<pre>' . (\Artisan::output()) . '</pre> ';
    }
}
