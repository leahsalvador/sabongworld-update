<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\Winner;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\BettingHistory;
use App\Models\GameRound;

ini_set('memory_limit', '-1');

class PlayerController extends Controller
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
    public function wallet()
    {
        $user = Auth::user();
        $transactions = Transaction::where('player_id', $user->id)->skip(0)->limit(10)->get();
        $payment_methods = PaymentMethod::where('user_id', $user->agentId()->first()->id)->get();
        $data = [
            'wallet' => $user->wallet,
            'player' => $user,
            'transactions' => $transactions,
            'paymentMethods' => $payment_methods,
        ];
        return view('/player/wallet', $data);
    }

    public function withdraw(Request $request)
    {
        $user = Auth::user();
        $logs = $user->transactions_withdraw->where('type', 'wallet');
        $page = isset($request->page) ? $request->page : 1;
        $skip = ($page * 5) - 5;
        $total_page = count($logs->toArray()) < 5 ? 1 : count($logs->toArray()) / 5;
        $total_page = $total_page > (int)(count($logs->toArray()) / 5) ? (int)(count($logs->toArray()) / 5) + 1 : $total_page;
        $limit = 5;
        $comission_logs = $logs->skip($skip)->take($limit);
        $users = $user->user_under()->where('status', 'activated')->get();
        $data = ['withdraw_logs' => $comission_logs, 'current_page' => $page, 'total_page' => $total_page, 'users' => $users];
        return view('/player/pages/withdraw', $data);
    }

    public function bettingHistory(Request $request)
    {
        $user = Auth::user();
        $game_bets = $user->bettingHistory->where('status', '!=', 'ongoing')->pluck('game_rounds_id')->toArray();
        $game_ids = array_values(array_unique($game_bets));
        $logs = GameRound::whereIn('id', $game_ids)->orderBy('created_at', 'desc')->get();
        // return $logs;
        $page = isset($request->page) ? $request->page : 1;
        $skip = ($page * 5) - 5;
        $total_page = count($logs->toArray()) < 5 ? 1 : count($logs->toArray()) / 5;
        $total_page = $total_page > (int)(count($logs->toArray()) / 5) ? (int)(count($logs->toArray()) / 5) + 1 : $total_page;
        $limit = 5;
        $betting_logs = $logs->skip($skip)->take($limit);
        $users = $user->user_under()->where('status', 'activated')->get();
        $data = ['betting_logs' => $betting_logs, 'current_page' => $page, 'total_page' => $total_page, 'users' => $users];
        return view('/player/pages/bettingHistory', $data);
    }

    public function bettingDetails(Request $request)
    {
        $user = Auth::user();
        $details = BettingHistory::where('player_id', $user->id)->where('game_rounds_id', $request->game_id)->get();
        return response()->json(['data' => $details]);
    }

    public function postWithdraw(Request $request)
    {
        $user = Auth::user();
        Validator::make($request->all(), [
            'amount' => ['required', 'numeric', 'lte:' . $user->wallet->points],
            'details' => ['required', 'string'],
        ])->validate();
        $transaction = new Transaction;
        $transaction->user_from = $user->id;
        $transaction->user_to = $user->agentId()->first()->id;
        $transaction->details = $request->details;
        $transaction->amount = $request->amount;
        $transaction->type = 'wallet';
        $transaction->transaction_type = 'withdraw';
        $transaction->transaction_status = 'pending';
        // return $transaction;
        if ($transaction->save()) {
            Wallet::where('user_id', $user->id)->update(['points' => floatval($user->wallet->points - $request->amount)]);
            return redirect('/player/withdraw');
        }
    }
    // public function deposit(Request $request)
    // {
    //     Validator::make($request->all(), [
    //         'agent_id' => ['required', 'numeric'],
    //         'payment_method_id' => ['required', 'numeric'],
    //         'amount' => ['required', 'numeric'],
    //     ])->validate();
    // //     Validator::make($request->all(), [
    // //         'agent_id' => ['required', 'numeric'],
    // //         'account_number' => ['required', 'numeric'],
    // //         'account_name' => ['required', 'string'],
    // //         'account_type' => ['required', 'string'],
    // //         'amount' => ['required', 'numeric','lt:'.$player_id->wallet->points],
    // //         ])->validate();
    //     $player_id = Auth::id();
    //     $transaction = new Transaction;
    //     $transaction->player_id = $player_id;
    //     $transaction->agent_id = $request->agent_id;
    //     $transaction->payment_method_id = $request->payment_method_id;
    //     $transaction->amount = $request->amount;
    //     $transaction->transaction_type = 'deposit';
    //     $transaction->transaction_status = 'pending';
    //     if($transaction->save()){
    //         return redirect('/player/wallet');
    //     }

    // }
    // public function withdraw(Request $request)
    // {
    //     // return $request;
    //     $player_id = Auth::user();
    //     Validator::make($request->all(), [
    //         'agent_id' => ['required', 'numeric'],
    //         'account_number' => ['required', 'numeric'],
    //         'account_name' => ['required', 'string'],
    //         'account_type' => ['required', 'string'],
    //         'amount' => ['required', 'numeric','lt:'.$player_id->wallet->points],
    //         ])->validate();
    //     $transaction = new Transaction;
    //     $transaction->player_id = $player_id->id;
    //     $transaction->agent_id = $request->agent_id;
    //     $transaction->account_number = $request->account_number;
    //     $transaction->account_name = $request->account_name;
    //     $transaction->account_type = $request->account_type;
    //     $transaction->amount = $request->amount;
    //     $transaction->transaction_type = 'withdraw';
    //     $transaction->transaction_status = 'pending';
    //     if($transaction->save()){
    //         Wallet::where('user_id',$player_id->id)->update(['points'=>floatval($player_id->wallet->points - $request->amount)]);
    //         return redirect('/player/wallet');
    //     }
    // }

    public function betting(Request $request)
    {
        return redirect()->back()->with(['message' => 'bet posted', 'status' => true]);
    }

    public function bet(Request $request)
    {
        try {
            $user = Auth::user();
            Validator::make($request->all(), [
                'side' => ['required', 'string'],
                'amount' => ['required', 'numeric', 'lte:' . $user->wallet->points],
                'game_rounds_id' => ['required', 'numeric'],
            ])->validate();
            $game_rounds_current = GameRound::where('id', $request->game_rounds_id)->first();
            if (in_array($game_rounds_current->status, ['open', 'final-bet'])) {
                $bet = $user->bettingHistory->where('game_rounds_id', $request->game_rounds_id)->where('side', $request->side)->first();
                if ($bet) {
                    $bet->amount = $bet->amount + $request->amount;
                    if ($bet->save()) {
                        Wallet::where('user_id', $user->id)->update(['points' => floatval($user->wallet->points - $request->amount)]);
                        //referral
                        $refferer = User::where('code', $user->referral_id)->first();
                        if (!empty($refferer)) {
                            if ($refferer->user_level == 'sub-agent') {
                                //check master agent
                                $updated = Wallet::where('user_id', $refferer->id)->update([
                                    'comission' => floatval($refferer->wallet->comission + ($request->amount * .03))
                                ]);
                                if ($updated) {
                                    $comLogs = Commission::create([
                                        "round" => $game_rounds_current->round,
                                        "from" => $user->id,
                                        "to" => $refferer->id,
                                        "amount" => ($request->amount * 0.03),
                                        "commission_percentage" => (100 * 0.03)
                                    ]);
                                }
                                $ma = User::where('code', $refferer->referral_id)->first();
                                if (!empty($ma)) {
                                    $updated = Wallet::where('user_id', $ma->id)->update([
                                        'comission' => floatval($ma->wallet->comission + ($request->amount * .02))
                                    ]);
                                    if ($updated) {
                                        $comLogs = Commission::create([
                                            "round" => $game_rounds_current->round,
                                            "from" => $user->id,
                                            "to" => $ma->id,
                                            "amount" => ($request->amount * 0.02),
                                            "commission_percentage" => (100 * 0.02)
                                        ]);
                                    }
                                }
                            } else {
                                $updated = Wallet::where('user_id', $refferer->id)->update([
                                    'comission' => floatval($refferer->wallet->comission + ($request->amount * .05))
                                ]);
                                if ($updated) {
                                    $comLogs = Commission::create([
                                        "round" => $game_rounds_current->round,
                                        "from" => $user->id,
                                        "to" => $refferer->id,
                                        "amount" => ($request->amount * 0.05),
                                        "commission_percentage" => (100 * 0.05)
                                    ]);
                                }
                            }
                        }
                        return response()->json(array('message' => 'bet posted', 'state' => true));
                    }
                } else {
                    $new_bet = new BettingHistory;
                    $new_bet->player_id = $user->id;
                    $new_bet->game_rounds_id = $request->game_rounds_id;
                    $new_bet->side = $request->side;
                    $new_bet->amount = $request->amount;
                    $new_bet->status = 'ongoing';
                    if ($new_bet->save()) {
                        $wallet = Wallet::where('user_id', $user->id)->update(['points' => floatval(auth()->user()->wallet->points - $request->amount)]);
                        BettingHistory::where('player_id', $user->id)->where('game_rounds_id', $request->game_rounds_id)->update(['current_points' => auth()->user()->wallet->points]);
                        // return redirect()->route('player');

                        $heads = BettingHistory::where("game_rounds_id", $request->game_rounds_id)->where("side", "heads")->sum('amount');
                        $tails = BettingHistory::where("game_rounds_id", $request->game_rounds_id)->where("side", "tails")->sum('amount');
                        $amtHeads = BettingHistory::where("game_rounds_id", $game_rounds_current->id)->where("side", "heads")->count();
                        $amtTails = BettingHistory::where("game_rounds_id", $game_rounds_current->id)->where("side", "tails")->count();

                        if ($amtHeads > 0 && $amtTails > 0) {
                            if ($tails > 0) {
                                $payout_tails = (($heads + $tails) / $tails) * config('settings.conversion_rate') * 100;
                            } else {
                                $payout_tails = 0;
                            }

                            if ($heads > 0) {
                                $payout_heads = (($heads + $tails) / $heads) * config('settings.conversion_rate') * 100;
                            } else {
                                $payout_heads = 0;
                            }
                        } else {
                            $payout_heads = 0;
                            $payout_tails = 0;
                        }

                        GameRound::whereIn('status', ['open'])->where('id', $game_rounds_current->id)->update([
                            'head_payout' => $payout_heads,
                            'tails_payout' => $payout_tails,
                            'total_bet_heads' => $heads,
                            'total_bet_tails' => $tails,
                        ]);
                        // return redirect()->back()->with(['message' => 'bet posted', 'status' => true]);
                        return response()->json(array('message' => 'bet posted', 'state' => true));
                    }
                }

            } else {
                return response()->json(array('message' => 'posting failed', 'state' => false), 404);
            }

        } catch (Exception $e) {
            return response()->json(array('message' => 'posting failed', 'state' => false), 401);
        }

    }

    public function declareWinner(Request $request)
    {
        $user = Auth::user();
        $winner = Winner::where('player_id', $user->id)->get();
        if ($winner->count()) {
            Winner::where('player_id', $user->id)->delete();
            return $winner->get(0);
        }
        return [];
    }
}
