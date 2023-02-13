<?php

namespace App\Http\Controllers;

use App\Models\PointHistory;
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
use App\Models\Registration;
use App\Http\Requests\WalletRequest;
use App\Http\Requests\ComissionRequest;
use App\Http\Requests\WithdrawalRequest;
use App\Rules\CurrentPasswordCheckRule;
use App\Notifications\RequestApproved;
use App\Notifications\Activated;
use App\Notifications\Deactivated;
use Notification;

class AgentController extends Controller
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
    public function summary()
    {
        $user = Auth::user();

        return view('agent.pages.summary');
    }

    public function comissionLogs(Request $request)
    {
        $user = Auth::user();
        $ids = config('settings.bots.agent') . ',' . config('settings.bots.meron') . ',' . config('settings.bots.wala');
        $ids = explode(",", $ids);

        $logs = DB::table('commissions as c')
            ->leftJoin('users as f', 'f.id', '=', 'c.from')
            ->leftJoin('users as t', 't.id', '=', 'c.to')
            ->whereNotIn('c.from', $ids)
            ->where('to', $user->id)
            ->select('c.*', 'f.name as fname', 'f.user_level as ftype', 't.name as tname', 't.user_level as ttype')
            ->orderByDesc('c.id')
            ->get();
        $data = ['logs' => $logs];
        return view('agent.pages.comissionLogs', $data);
    }

    public function wallet()
    {
        $user = Auth::user();
        $ids = config('settings.bots.agent') . ',' . config('settings.bots.meron') . ',' . config('settings.bots.wala');
        $ids = explode(",", $ids);
        $user_under = $user->user_under()->where('status', 'activated')->whereNotIn('id', $ids)->get();
        $data = ['users' => $user_under];
        return view('agent.pages.wallet', $data);
    }

    public function wallet_modify(WalletRequest $request)
    {

        $validated = $request->validated();
        $user = Auth::user();
        $load_to = User::where('id', $request->load_to)->first();

        $transaction = new Transaction;

        if ($request->transaction_type == 'withdraw') {

            $transaction->user_from = $load_to->id;
            $transaction->user_to = $user->id;
            if ($load_to->user_level == 'master-agent-player' || $load_to->user_level == 'sub-agent-player') {
                $transaction->transaction_type = 'withdraw';
            } else if ($load_to->user_level == 'sub-agent') {
                $transaction->transaction_type = 'agent-withdraw';
            } else {
                $transaction->transaction_type = 'system-withdraw';
            }
        } else {
            $transaction->user_from = $user->id;
            $transaction->user_to = $load_to->id;
            if ($load_to->user_level == 'master-agent-player' || $load_to->user_level == 'sub-agent-player') {
                $transaction->transaction_type = 'deposit';
            } else if ($load_to->user_level == 'sub-agent') {
                $transaction->transaction_type = 'agent-deposit';
            } else {
                $transaction->transaction_type = 'system-deposit';
            }
        }


        $transaction->amount = $request->amount;
        $transaction->details = $request->details;
        $transaction->type = 'wallet';

        $transaction->transaction_status = 'success';
        if ($transaction->save()) {
            if ($request->transaction_type == 'withdraw') {
                $wallet = Wallet::where('user_id', $request->load_to)->first();
                $wallet->points = floatval($wallet->points - $request->amount);
                if ($wallet->save()) {
                    $player_wallet = Wallet::where('user_id', $user->id)->first();
                    $player_wallet->points = floatval($player_wallet->points + $request->amount);
                    $player_wallet->save();

                    PointHistory::create([
                        'tid' => $transaction->id,
                        'user_id' => $wallet->user_id,
                        'points' => $wallet->points
                    ]);
                    PointHistory::create([
                        'tid' => $transaction->id,
                        'user_id' => $player_wallet->user_id,
                        'points' => $player_wallet->points
                    ]);

                }
                return redirect()->back()->with('success', 'withdraw success');
            } else {
                $wallet = Wallet::where('user_id', $request->load_to)->first();
                $wallet->points = floatval($wallet->points + $request->amount);
                if ($wallet->save()) {
                    $player_wallet = Wallet::where('user_id', $user->id)->first();
                    $player_wallet->points = floatval($player_wallet->points - $request->amount);
                    $player_wallet->save();

                    PointHistory::create([
                        'tid' => $transaction->id,
                        'user_id' => $wallet->user_id,
                        'points' => $wallet->points
                    ]);
                    PointHistory::create([
                        'tid' => $transaction->id,
                        'user_id' => $player_wallet->user_id,
                        'points' => $player_wallet->points
                    ]);
                }
                return redirect()->back()->with('success', 'deposit success');
            }
        }
    }

    public function walletLogs(Request $request)
    {
        $user = Auth::user();
        if ($request->transaction_type || $request->username) {
            $transaction_type = $request->transaction_type ? $request->transaction_type : null;
            $username = $request->username ? $request->username : null;
            $id = User::where('name', 'like', '%' . $username . '%')->get()->pluck('id');
            if ($transaction_type && $username) {
                $from = Transaction::where('user_from', $user->id)
                    ->leftJoin('point_history as ph', 'ph.tid', '=', 'transactions.id')
                    ->where('type', 'wallet')
                    ->where('transaction_type', $transaction_type)
                    ->whereIn('user_from', $id)
                    ->whereIn('ph.user_id', $id);
                $logs = Transaction::where('user_to', $user->id)
                    ->leftJoin('point_history as ph', 'ph.tid', '=', 'transactions.id')
                    ->where('type', 'wallet')
                    ->where('transaction_type', $transaction_type)
                    ->union($from)
                    ->whereIn('user_from', $id)
                    ->whereIn('ph.user_id', $id)
                    ->get();
            } else if ($username) {
                $from = Transaction::where('user_from', $user->id)
                    ->leftJoin('point_history as ph', 'ph.tid', '=', 'transactions.id')
                    ->where('type', 'wallet')
                    ->whereIn('user_from', $id)
                    ->whereIn('ph.user_id', $id);
                $logs = Transaction::where('user_to', $user->id)
                    ->leftJoin('point_history as ph', 'ph.tid', '=', 'transactions.id')
                    ->where('type', 'wallet')
                    ->union($from)
                    ->whereIn('user_from', $id)
                    ->whereIn('ph.user_id', $id)
                    ->get();
            } else if ($transaction_type) {
                $from = Transaction::where([
                    ['user_from', '=', $user->id],
                    ['ph.user_id', '=', $user->id]
                ])
                    ->leftJoin('point_history as ph', 'ph.tid', '=', 'transactions.id')
                    ->where('type', 'wallet')
                    ->where('transaction_type', $transaction_type);
                $logs = Transaction::where([
                    ['user_to', '=', $user->id],
                    ['ph.user_id', '=', $user->id]
                ])
                    ->leftJoin('point_history as ph', 'ph.tid', '=', 'transactions.id')
                    ->where('type', 'wallet')
                    ->union($from)->where('transaction_type', $transaction_type)->get();
            }
        } else {
            $from = Transaction::where([
                ['user_from', '=', $user->id],
                ['ph.user_id', '=', $user->id]
            ])
                ->leftJoin('point_history as ph', 'ph.tid', '=', 'transactions.id')
                ->where('type', 'wallet');
            $logs = Transaction::where([
                ['user_to', '=', $user->id],
                ['ph.user_id', '=', $user->id]
            ])->where('type', 'wallet')
                ->leftJoin('point_history as ph', 'ph.tid', '=', 'transactions.id')
                ->union($from)->get();
        }

        $page = isset($request->page) ? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($logs->toArray()) < 10 ? 1 : count($logs->toArray()) / 10;
        $total_page = $total_page > (int)(count($logs->toArray()) / 10) ? (int)(count($logs->toArray()) / 10) + 1 : $total_page;
        $limit = 10;

        $wallet_logs = $logs->sortByDesc('created_at');
        $users = $user->user_under()->where('status', 'activated')->get();

        $data = ['wallet_logs' => $wallet_logs, 'current_page' => $page, 'total_page' => $total_page, 'users' => $users];
        return view('agent.pages.walletLogs', $data);
    }

    public function comission()
    {
        $user = Auth::user();
        $user_under = $user->user_under()->where('status', 'activated')->whereIn('user_level', ['master-agent', 'sub-agent', 'gold-agent', 'silver-agent'])->get();
        $data = ['users' => $user_under];
        return view('agent.pages.comission', $data);
    }

    public function comission_modify(ComissionRequest $request)
    {
        $validated = $request->validated();

        $user = Auth::user();
        $load_to = User::where('id', $request->load_to)->first();
        $transaction = new Transaction;
        if ($request->transaction_type == 'withdraw') {

            $transaction->user_from = $load_to->id;
            $transaction->user_to = $user->id;
        } else {
            $transaction->user_from = $user->id;
            $transaction->user_to = $load_to->id;

        }
        $transaction->amount = $request->amount;
        $transaction->details = $request->details;
        $transaction->type = 'comission';
        $transaction->transaction_type = $request->transaction_type;
        $transaction->transaction_status = 'success';
        if ($transaction->save()) {
            if ($request->transaction_type == 'withdraw') {
                $wallet = Wallet::where('user_id', $request->load_to)->first();
                $wallet->comission = floatval($wallet->comission - $request->amount);
                if ($wallet->save()) {
                    $player_wallet = Wallet::where('user_id', $user->id)->first();
                    $player_wallet->comission = floatval($player_wallet->comission + $request->amount);
                    $player_wallet->save();
                }
                return redirect()->back()->with('success', 'withdraw success');
            } else {
                $wallet = Wallet::where('user_id', $request->load_to)->first();
                $wallet->comission = floatval($wallet->comission + $request->amount);
                if ($wallet->save()) {
                    $player_wallet = Wallet::where('user_id', $user->id)->first();
                    $player_wallet->comission = floatval($player_wallet->comission - $request->amount);
                    $player_wallet->save();
                }

                return redirect()->back()->with('success', 'deposit success');
            }
            // return redirect(route('agent-wallet'));
        }
    }

    public function comissionLog(Request $request)
    {
        $user = Auth::user();
        if ($request->transaction_type || $request->member) {
            $transaction_type = $request->transaction_type ? $request->transaction_type : null;
            $username = $request->member ? $request->member : null;
            $id = User::where('name', 'like', '%' . $username . '%')->get()->pluck('id');
            if ($transaction_type && $username) {
                $from = Transaction::where('user_from', $user->id)->where('type', 'comission')->where('transaction_type', $transaction_type)->whereIn('user_from', $id)->where('deleted_at', null);
                $logs = Transaction::where('user_to', $user->id)->where('type', 'comission')->where('transaction_type', $transaction_type)->union($from)->whereIn('user_from', $id)->where('deleted_at', null)->get();
            } else if ($username) {
                $from = Transaction::where('user_from', $user->id)->where('type', 'comission')->whereIn('user_from', $id)->where('deleted_at', null);
                $logs = Transaction::where('user_to', $user->id)->where('type', 'comission')->union($from)->whereIn('user_from', $id)->where('deleted_at', null)->get();
            } else if ($transaction_type) {
                $from = Transaction::where('user_from', $user->id)->where('type', 'comission')->where('transaction_type', $transaction_type)->where('deleted_at', null);
                $logs = Transaction::where('user_to', $user->id)->where('type', 'comission')->union($from)->where('transaction_type', $transaction_type)->where('deleted_at', null)->get();
            }
        } else {
            $from = Transaction::where('user_from', $user->id)->where('type', 'comission')->where('deleted_at', null);
            $logs = Transaction::where('user_to', $user->id)->where('type', 'comission')->union($from)->where('deleted_at', null)->get();
        }
        $page = isset($request->page) ? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($logs->toArray()) < 10 ? 1 : count($logs->toArray()) / 10;
        $total_page = $total_page > (int)(count($logs->toArray()) / 10) ? (int)(count($logs->toArray()) / 10) + 1 : $total_page;
        $limit = 10;
        // $comission_logs = $logs->sortByDesc('created_at')->skip($skip)->take($limit);
        $comission_logs = $logs->sortByDesc('created_at');
        $users = $user->user_under()->where('status', 'activated')->get();
        $data = ['comission_logs' => $comission_logs, 'current_page' => $page, 'total_page' => $total_page, 'users' => $users];

        return view('agent.pages.comissionLog', $data);
    }

    public function comissionArchive(Request $request)
    {
        $user = Auth::user();
        if ($request->transaction_type || $request->member) {
            $transaction_type = $request->transaction_type ? $request->transaction_type : null;
            $username = $request->member ? $request->member : null;
            $id = User::where('name', 'like', '%' . $username . '%')->get()->pluck('id');
            if ($transaction_type && $username) {
                $from = Transaction::where('user_from', $user->id)->where('type', 'comission')->whereIn('user_from', $id)->where('deleted_at', '!=', null);
                $logs = Transaction::where('user_to', $user->id)->where('type', 'comission')->where('transaction_type', $transaction_type)->union($from)->whereIn('user_from', $id)->where('deleted_at', '!=', null)->get();
            } else if ($username) {
                $from = Transaction::where('user_from', $user->id)->where('type', 'comission')->whereIn('user_from', $id)->where('deleted_at', '!=', null);
                $logs = Transaction::where('user_to', $user->id)->where('type', 'comission')->union($from)->whereIn('user_from', $id)->where('deleted_at', null)->get();
            } else if ($transaction_type) {
                $from = Transaction::where('user_from', $user->id)->where('type', 'comission')->where('deleted_at', '!=', null);
                $logs = Transaction::where('user_to', $user->id)->where('type', 'comission')->union($from)->where('transaction_type', $transaction_type)->where('deleted_at', '!=', null)->get();
            }
        } else {
            $from = Transaction::where('user_from', $user->id)->where('type', 'comission')->where('deleted_at', '!=', null);
            $logs = Transaction::where('user_to', $user->id)->where('type', 'comission')->union($from)->where('deleted_at', '!=', null)->get();
        }
        $page = isset($request->page) ? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($logs->toArray()) < 10 ? 1 : count($logs->toArray()) / 10;
        $total_page = $total_page > (int)(count($logs->toArray()) / 10) ? (int)(count($logs->toArray()) / 10) + 1 : $total_page;
        $limit = 10;
        // $comission_logs = $logs->sortByDesc('created_at')->skip($skip)->take($limit);
        $comission_logs = $logs->sortByDesc('created_at');
        $users = $user->user_under()->where('status', 'activated')->get();
        $data = ['comission_logs' => $comission_logs, 'current_page' => $page, 'total_page' => $total_page, 'users' => $users];

        return view('agent.pages.comissionArchive', $data);

    }

    public function activePlayers(Request $request)
    {
        $user = Auth::user();
        $ids = config('settings.bots.agent') . ',' . config('settings.bots.meron') . ',' . config('settings.bots.wala');
        $ids = explode(",", $ids);

        $users = DB::table('users AS u')->where("u.referral_id", $user->code)
            ->leftJoin('wallets AS w', 'u.id', '=', 'w.user_id')
            ->where('u.status', 'activated')
            ->whereIn('u.user_level', ['master-agent-player', 'sub-agent-player', 'gold-agent-player', 'silver-agent-player'])
            ->whereNotIn('u.id', $ids)
            ->select('u.*', 'w.*', DB::raw('CONCAT("' . route('player.history', '') . '/", u.id) AS hurl'), DB::raw("DATE_FORMAT(u.created_at, '%M %d, %Y') AS registered"))
            ->orderByDesc('u.created_at')
            ->get();
        $data = [
            'users' => $users,
            'display' => config('settings.role.display'),
        ];
        return view('agent.pages.activePlayers', $data);
    }

    public function changePlayerStatus(Request $request)
    {

        Validator::make($request->all(), [
            'user_id' => ['required', 'numeric'],
            'status' => ['required', 'string'],
            'password' => ['required', 'min:6', new CurrentPasswordCheckRule]
        ])->validate();
        $user_data = User::withTrashed()->where('id', $request->user_id)->first();
        $user_name = $user_data->name;
        if ($request->status == 'deactivated') {
            $user = User::where('id', $request->user_id)->update(['status' => $request->status]);
            // Notification::send($user_data, new Deactivated());
            $user_data->delete();
        } else {
            $user_data->restore();
            $user = User::where('id', $request->user_id)->update(['status' => $request->status]);
            // Notification::send($user_data, new Activated());

        }
        return redirect()->back()->with('success', '' . $user_name . 'user ' . $request->status);
    }

    public function changeLevel(Request $request)
    {
        $user = Auth::user();
        Validator::make($request->all(), [
            'user_id' => ['required', 'numeric'],
            'user_level' => ['required', 'string'],
            'password' => ['required', 'min:6', new CurrentPasswordCheckRule]
        ])->validate();

        $user = User::where('id', $request->user_id)->first();
        if (!$user->code) {
            $bytes = random_bytes(20);
            $user->code = bin2hex($bytes);
        }
        $user->user_level = $request->user_level;
        $user->save();
        return redirect()->back()->with('success', '' . $user->name . ' changed level to ' . $request->user_level);
    }

    public function userApproveConfirm(Request $request)
    {
        $user = Auth::user();
        $registration = Registration::where('id', $request->user_id)->first();
        $status = $request->status == 'Approve' ? 'activated' : 'deactivated';
        $soft_delete = null;
        if ($status == 'deactivated') {
            $soft_delete = now();
        }
        if ($registration->deleted_at != null) {
            return redirect()->back()->with('success', '' . $registered->name . ' has already been accepted');
        }
        $registered = User::create([
            'name' => $registration->name,
            'username' => $registration->username,
            'email' => $registration->email,
            'phone_number' => $registration->phone_number,
            'facebook_link' => $registration->facebook_link,
            'password' => $registration->password,
            'user_level' => $registration->user_level,
            'status' => $status,
            'referral_id' => $registration->referral_id,
            'deleted_at' => $soft_delete

        ]);
        Wallet::create([
            'user_id' => $registered->id,
            'points' => 0,
            'comission' => 0,
        ]);
        $registration->delete();
        return redirect()->back()->with('success', '' . $registered->name . ' has been ' . $request->status);
    }

    public function userApproval(Request $request)
    {
        $user = Auth::user();
        $username = $request->username ? $request->username : '';
        if ($username) {
            $user_id1 = Registration::where('name', 'like', '%' . $username . '%')->get()->pluck('id')->toArray();
            $user_id2 = Registration::where('username', 'like', '%' . $username . '%')->get()->pluck('id')->toArray();
            $user_id = array_merge($user_id1, $user_id2);
            $users = $user->user_approval->whereIn('id', $user_id);
        } else {
            $users = $user->user_approval;
        }
        $page = isset($request->page) ? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($users->toArray()) < 10 ? 1 : count($users->toArray()) / 10;
        $total_page = $total_page > (int)(count($users->toArray()) / 10) ? (int)(count($users->toArray()) / 10) + 1 : $total_page;
        $limit = 10;
        // $users = $users->sortByDesc('created_at')->skip($skip)->take($limit);
        $users = $users->sortByDesc('created_at');
        $data = ['users' => $users, 'current_page' => $page, 'total_page' => $total_page];

        return view('agent.pages.userApproval', $data);
    }

    public function deactivatedPlayers(Request $request)
    {
        $user = Auth::user();
        $username = $request->username ? $request->username : '';
        if ($username) {
            $user_id1 = User::withTrashed()->where('name', 'like', '%' . $username . '%')->get()->pluck('id')->toArray();
            $user_id2 = User::withTrashed()->where('username', 'like', '%' . $username . '%')->get()->pluck('id')->toArray();
            $user_id = array_merge($user_id1, $user_id2);
            $users = $user->user_under()->withTrashed()->where('status', 'deactivated')->whereIn('id', $user_id)->get();
        } else {
            $users = $user->user_under()->withTrashed()->where('status', 'deactivated')->get();
        }


        $page = isset($request->page) ? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($users->toArray()) < 10 ? 1 : count($users->toArray()) / 10;
        $total_page = $total_page > (int)(count($users->toArray()) / 10) ? (int)(count($users->toArray()) / 10) + 1 : $total_page;
        $limit = 10;
        // $users = $users->sortByDesc('created_at')->skip($skip)->take($limit);
        $users = $users->sortByDesc('created_at');
        $data = ['users' => $users, 'current_page' => $page, 'total_page' => $total_page];

        return view('agent.pages.deactivated', $data);
    }

    public function withdrawalRequest(Request $request)
    {
        $user = Auth::user();
        $username = $request->username ? $request->username : '';

        if ($username) {
            $id = User::where('username', 'like', '%' . $username . '%')->get()->pluck('id');
            $users = $user->transactions->whereIn('user_from', $id)->where('type', 'wallet')->where('transaction_type', 'withdraw')->where('transaction_status', 'pending');
        } else {
            $users = $user->transactions->where('type', 'wallet')->where('transaction_type', 'withdraw')->where('transaction_status', 'pending');
        }

        $page = isset($request->page) ? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($users->toArray()) < 10 ? 1 : count($users->toArray()) / 10;
        $total_page = $total_page > (int)(count($users->toArray()) / 10) ? (int)(count($users->toArray()) / 10) + 1 : $total_page;
        $limit = 10;
        // $users = $users->sortByDesc('created_at')->skip($skip)->take($limit);
        $users = $users->sortByDesc('created_at');
        $players = $user->user_under()->where('status', 'activated')->whereNotIn('user_level', ['sub-agent'])->get();

        $data = ['users' => $users, 'current_page' => $page, 'total_page' => $total_page, 'players' => $players];

        return view('agent.pages.withdrawalRequests', $data);
    }

    public function withdrawalRequestConfirm(WithdrawalRequest $request)
    {
        $validated = $request->validated();

        $user = Auth::user();
        $load_to = Transaction::where('id', $request->transaction_id)->first();
        if ($request->type == 'Reject') {
            $load_to->transaction_status = 'cancelled';
            $load_to->save();
            Wallet::where('user_id', $load_to->from->id)->update(['points' => floatval($load_to->from->wallet->points + $load_to->amount)]);
            PointHistory::create([
                'tid' => $request->transaction_id,
                'user_id' => $load_to->from->id,
                'points' => floatval($load_to->from->wallet->points + $load_to->amount)
            ]);

            return redirect()->back()->with('failed', 'Request rejected !');
        } else {
            $load_to->transaction_status = 'success';
            $load_to->save();
            Wallet::where('user_id', $user->id)->update(['points' => floatval($user->wallet->points + $load_to->amount)]);
            PointHistory::create([
                'tid' => $request->transaction_id,
                'user_id' => $user->id,
                'points' => floatval($user->wallet->points + $load_to->amount)
            ]);
            return redirect()->back()->with('success', 'Request approved !');
        }
        // return redirect(route('agent-wallet'));
    }

    public function withdrawalRequestHistory(Request $request)
    {
        $user = Auth::user();
        $username = $request->username ? $request->username : '';

        if ($username) {
            $id = User::where('username', 'like', '%' . $username . '%')->get()->pluck('id');
            $request_histories = $user->transactions->whereIn('user_from', $id)->where('type', 'wallet')->where('transaction_type', 'withdraw');
        } else {
            $request_histories = $user->transactions->where('type', 'wallet')->where('transaction_type', 'withdraw');
        }
        $page = isset($request->page) ? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($request_histories->toArray()) < 10 ? 1 : count($request_histories->toArray()) / 10;
        $total_page = $total_page > (int)(count($request_histories->toArray()) / 10) ? (int)(count($request_histories->toArray()) / 10) + 1 : $total_page;
        $limit = 10;
        // $request_histories = $request_histories->sortByDesc('created_at')->skip($skip)->take($limit);
        $request_histories = $request_histories->sortByDesc('created_at');
        $players = $user->user_under()->where('status', 'activated')->whereNotIn('user_level', ['sub-agent'])->get();

        $data = ['request_histories' => $request_histories, 'current_page' => $page, 'total_page' => $total_page, 'players' => $players];

        return view('agent.pages.requestHistory', $data);
    }

    public function agentList(Request $request)
    {
        $user = Auth::user();
        $username = $request->username ? $request->username : '';
        if ($username) {
            $user_id1 = User::where('name', 'like', '%' . $username . '%')->get()->pluck('id')->toArray();
            $user_id2 = User::where('username', 'like', '%' . $username . '%')->get()->pluck('id')->toArray();
            $user_id = array_merge($user_id1, $user_id2);
            $users = $user->user_under()->whereIn('id', $user_id)->whereIn('user_level', ['sub-agent', 'gold-agent', 'silver-agent'])->get();
        } else {
            $users = $user->user_under()->whereIn('user_level', ['sub-agent', 'gold-agent', 'silver-agent'])->get();
        }

        $page = isset($request->page) ? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($users->toArray()) < 10 ? 1 : count($users->toArray()) / 10;
        $total_page = $total_page > (int)(count($users->toArray()) / 10) ? (int)(count($users->toArray()) / 10) + 1 : $total_page;
        $limit = 10;
        // $users = $users->sortByDesc('created_at')->skip($skip)->take($limit);
        $users = $users->sortByDesc('created_at');
        $data = ['users' => $users, 'current_page' => $page, 'total_page' => $total_page];

        return view('agent.pages.agentList', $data);
    }

    public function comissionEdit(Request $request)
    {
        $user = Auth::user();
        $max = 0;
        $min = 0;
        switch ($user->user_level) {
            case 'master-agent':
                $max = config('settings.commission.max.sub');
                $min = config('settings.commission.min.sub');
                break;
            case 'sub-agent':
                $max = config('settings.commission.max.gold');
                $min = config('settings.commission.min.gold');
                break;
            case 'gold-agent':
                $max = config('settings.commission.max.silver');
                $min = config('settings.commission.min.silver');
                break;
            case 'silver-agent':
                $max = config('settings.commission.max.bronze');
                $min = config('settings.commission.min.bronze');
                break;
        }
        $agents = User::whereIn('user_level', ['sub-agent', 'gold-agent', 'silver-agent'])->where([
            ['referral_id', $user->code],
            ['status', 'activated']
        ])->get();

        $data = [
            'agents' => $agents,
            'user' => $user,
            'updateUrl' => route('agent-update-comission'),
            'max_commission' => ($user->commission_percent > 0) ? $user->commission_percent : $max,
            'min_commission' => $min,
            'dispay_name' => config('settings.role.display'),
        ];
        return view('agent.pages.comissionEdit', $data);
    }

    public function updateCommission(Request $request, $commission, $user_id)
    {
        $user = Auth::user();
        $max = 0;
        $min = 0;
        switch ($user->user_level) {
            case 'master-agent':
                $max = config('settings.commission.max.sub');
                $min = config('settings.commission.min.sub');
                break;
            case 'sub-agent':
                $max = config('settings.commission.max.gold');
                $min = config('settings.commission.min.gold');
                break;
            case 'gold-agent':
                $max = config('settings.commission.max.silver');
                $min = config('settings.commission.min.silver');
                break;
            case 'silver-agent':
                $max = config('settings.commission.max.bronze');
                $min = config('settings.commission.min.bronze');
                break;
        }
        $max = ($user->commission_percent > 0) ? $user->commission_percent : $max;

        if ($commission >= $min && $commission <= $max) {
            $user = User::find($user_id);
            $roles = ['sub-agent', 'gold-agent', 'silver-agent'];
            if (in_array($user->user_level, $roles)) {
                $user->commission_percent = floatval($commission);
                $updated = $user->save();
                if ($updated) {
                    return response()->json([
                        "status" => true,
                        "message" => "Update Successfully"
                    ]);
                }
            }
        }
        return response()->json([
            "status" => false,
            "message" => "Could not process your request"
        ], 400);
    }

    public function showTransactions(Request $request)
    {
        $user = Auth::user();
        $logs_from = DB::table('transactions as t')
            ->leftJoin('users as u', 'u.id', '=', 't.user_to')
            ->leftJoin('wallets as w', 'u.id', '=', 'w.user_id')
            ->where('user_from', $user->id)
            ->select('t.id as tid', 't.user_from as uid', 't.amount', 't.type', 't.transaction_type', 't.transaction_status',
                'u.name', 'u.commission_percent', 'u.user_level as user_type', 'w.points', 'w.comission', 't.created_at')
            ->orderByDesc('t.created_at')
            ->get();
        $logs_to = DB::table('transactions as t')
            ->leftJoin('users as u', 'u.id', '=', 't.user_from')
            ->leftJoin('wallets as w', 'u.id', '=', 'w.user_id')
            ->where('user_to', $user->id)
            ->select('t.id as tid', 't.user_from as uid', 't.amount', 't.type', 't.transaction_type', 't.transaction_status',
                'u.name', 'u.commission_percent', 'u.user_level as user_type', 'w.points', 'w.comission', 't.created_at')
            ->orderByDesc('t.created_at')
            ->get();
        $logs = $logs_from->merge($logs_to);
        $data = [
            'logs' => $logs
        ];
        return view('agent.transactions', $data);
    }

    public function showHistory(Request $request, $player_id)
    {
//        DB::enableQueryLog();
        $transLog = DB::table('transactions as t')
            ->leftJoin('point_history as ph', 'ph.tid', '=', 't.id')
            ->where([
                ['t.user_to', '=', $player_id],
                ['ph.user_id', '=', $player_id]
            ])
            ->orWhere([
                ['t.user_from', '=', $player_id],
                ['ph.user_id', '=', $player_id]
            ])
            ->select('t.id as tid', 't.amount', 't.transaction_type AS type', 't.transaction_status', DB::raw('IFNULL(ph.points, 0) AS points'), 't.created_at AS datetime')
            ->get();
        $betLog = DB::table('betting_histories as b')
            ->leftJoin('game_rounds AS g', 'g.id', '=', 'b.game_rounds_id')
            ->where('player_id', $player_id)
            ->whereIn('b.status', ['win', 'loose', 'draw'])
            ->select('b.id AS bid', 'g.id AS gid', 'b.amount', 'b.win_amount', 'b.loose_amount', 'b.current_points as points', 'b.status AS type',
                'b.created_at AS datetime', 'g.round')
            ->get();
        //dd($betLog);

        $betArcLog = DB::table('betting_archive as ba')
            ->leftJoin('game_archive AS g', 'g.id', '=', 'ba.game_rounds_id')
            ->where('player_id', $player_id)
            ->whereIn('ba.status', ['win', 'loose', 'draw'])
            ->select('ba.id AS bid', 'g.id AS gid', 'ba.amount', 'ba.win_amount', 'ba.loose_amount', 'ba.current_points as points', 'ba.status AS type',
                'ba.created_at AS datetime', 'g.round')
            ->orderByDesc('datetime')
            ->get();

        $logs = $transLog->merge($betLog)->merge($betArcLog)->sortBy('datetime', SORT_DESC, true);
        $userInfo = User::find($player_id);
//        dd(DB::getQueryLog());

        $data = [
            "logs" => $logs,
            "user" => $userInfo,
            "type" => [
                'win' => "Winnings/Bet",
                'loose' => "Losings/Bet",
                'draw' => "Draws/Bet",
                'deposit' => "Cash-In",
                'withdraw' => "Withdraw",
            ]
        ];
        return view('agent.pages.playerHistory', $data);
    }
}
