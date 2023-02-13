<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Exception;
use Notification;
use Carbon\Carbon;

use App\Models\Commission;
use App\Models\PointHistory;
use App\Models\Winner;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\PaymentMethod;
use App\Models\SiteSettings;
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


ini_set('memory_limit', '-1');

class AdminController extends Controller
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
            ->select('c.*', 'f.name as fname', 'f.user_level as ftype', 't.name as tname', 't.user_level as ttype')
            ->orderByDesc('c.id')
            ->get();

        $data = ['logs' => $logs];
        return view('agent.pages.comissionLogs', $data);
    }

    public function wallet()
    {
        $user = Auth::user();
        $user_under = $user->user_under()->where('status', 'activated')->where('user_level', 'master-agent')->get();
        $data = [
            'users' => $user_under,
            'minimum_deposit' => config('settings.minimum_deposit'),
        ];
        return view('admin.wallet', $data);
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
            $minimum_deposit = config('settings.minimum_deposit');
            if(!empty($minimum_deposit)){
                if($request->amount < $minimum_deposit){
                    return redirect()->back()->with('error', 'Minimum load 100 in wallet deposit');
                }
            }
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
                $updated = Wallet::where('user_id', $request->load_to)
                    ->update([
                        'points' => floatval($load_to->wallet->points - $request->amount)
                    ]);
                if ($updated) {
                    $updated = Wallet::where('user_id', $user->id)
                        ->update([
                            'points' => floatval($user->wallet->points + $request->amount)
                        ]);
                    if ($updated) {
                        PointHistory::create([
                            'tid' => $transaction->id,
                            'user_id' => $request->load_to,
                            'points' => floatval($load_to->wallet->points - $request->amount)
                        ]);
                        PointHistory::create([
                            'tid' => $transaction->id,
                            'user_id' => $user->id,
                            'points' => floatval($user->wallet->points + $request->amount)
                        ]);
                    }
                }
                return redirect()->back()->with('success', 'withdraw success');
            } else {
                $updated = Wallet::where('user_id', $request->load_to)->update(['points' => floatval($load_to->wallet->points + $request->amount)]);
                if ($updated) {
                    $updated = Wallet::where('user_id', $user->id)->update(['points' => floatval($user->wallet->points - $request->amount)]);
                    if ($updated) {
                        PointHistory::create([
                            'tid' => $transaction->id,
                            'user_id' => $request->load_to,
                            'points' => floatval($load_to->wallet->points + $request->amount)
                        ]);
                        PointHistory::create([
                            'tid' => $transaction->id,
                            'user_id' => $user->id,
                            'points' => floatval($user->wallet->points - $request->amount)
                        ]);
                    }
                }
                return redirect()->back()->with('success', 'deposit success');
            }
        }
    }

    public function walletLogs(Request $request)
    {
        $user = Auth::user();
        $userIds = DB::table('users')->whereIn('user_level', ['super-admin', 'admin'])->select('id')->pluck('id')->toArray();

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

            $from = Transaction::whereIn('user_from', $userIds)
                ->whereIn('ph.user_id', $userIds)
                ->leftJoin('point_history as ph', 'ph.tid', '=', 'transactions.id')
                ->where('type', 'wallet');
            $logs = Transaction::whereIn('user_to', $userIds)
                ->whereIn('ph.user_id', $userIds)
                ->where('type', 'wallet')
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
        $user_under = $user->user_under()->where('status', 'activated')->whereIn('user_level', ['sub-agent', 'master-agent'])->get();
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
        $transaction->transaction_type = 'system-' . $request->transaction_type;
        $transaction->transaction_status = 'success';
        if ($transaction->save()) {
            if ($request->transaction_type == 'withdraw') {
                Wallet::where('user_id', $request->load_to)->update(['comission' => floatval($load_to->wallet->comission - $request->amount)]);
                Wallet::where('user_id', $user->id)->update(['comission' => floatval($user->wallet->comission + $request->amount)]);
                return redirect()->back()->with('success', 'withdraw success');
            } else {
                Wallet::where('user_id', $request->load_to)->update(['comission' => floatval($load_to->wallet->comission + $request->amount)]);
                Wallet::where('user_id', $user->id)->update(['comission' => floatval($user->wallet->comission - $request->amount)]);
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
                $from = Transaction::where('user_from', $user->id)->where('type', 'comission')->whereIn('user_from', $id)->where('deleted_at', null);
                $logs = Transaction::where('user_to', $user->id)->where('type', 'comission')->where('transaction_type', $transaction_type)->union($from)->whereIn('user_from', $id)->where('deleted_at', null)->get();
            } else if ($username) {
                $from = Transaction::where('user_from', $user->id)->where('type', 'comission')->whereIn('user_from', $id)->where('deleted_at', null);
                $logs = Transaction::where('user_to', $user->id)->where('type', 'comission')->union($from)->whereIn('user_from', $id)->where('deleted_at', null)->get();
            } else if ($transaction_type) {
                $from = Transaction::where('user_from', $user->id)->where('type', 'comission')->where('deleted_at', null);
                $logs = Transaction::where('user_to', $user->id)->where('type', 'comission')->union($from)->where('transaction_type', $transaction_type)->where('deleted_at', null)->get();
            }
        } else {
            $from = Transaction::where('user_from', $user->id)
                ->where('type', 'comission')
                ->where('deleted_at', null)
                ->leftJoin('wallets as w', 'w.user_id', '=', 'transactions.user_from')
                ->select('transactions.*','w.comission');
            $logs = Transaction::where('user_to', $user->id)
                ->where('type', 'comission')
                ->union($from)
                ->where('deleted_at', null)
                ->leftJoin('wallets as w', 'w.user_id', '=', 'transactions.user_to')
                ->select('transactions.*','w.comission')
                ->get();
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

        $users = DB::table('users AS u')
            ->leftJoin('wallets AS w', 'u.id', '=', 'w.user_id')
            ->whereIn("u.user_level", ['master-agent-player', 'sub-agent-player', 'gold-agent-player', 'silver-agent-player'])
            ->whereNotIn('u.id', $ids)
            ->where('u.status', 'activated')
            ->select('u.*', 'w.*', DB::raw('CONCAT("' . route('admin.player.history', '') . '/", u.id) AS hurl'), DB::raw("DATE_FORMAT(u.created_at, '%M %d, %Y') AS registered"))
            ->orderByDesc('w.points')
            ->get();

        $data = [
            'users' => $users,
            'display' => config('settings.role.display')
        ];
        return view('agent.pages.activePlayers', $data);
    }

    public function clearactivePlayers(Request $request)
    {
        $user = Auth::user();
        $name = $request->username ? $request->username : '';
        $users_id = User::where('name', 'like', '%' . $name . '%')->orWhere('username', 'like', '%' . $name . '%')->get()->pluck('id');
        $users = User::where('status', 'activated')->whereIn('user_level', ['master-agent-player', 'sub-agent-player'])->whereIn('id', $users_id)->delete();
        $users = User::where('status', 'activated')->whereIn('user_level', ['master-agent-player', 'sub-agent-player'])->whereIn('id', $users_id)->get();
        $page = isset($request->page) ? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($users->toArray()) < 10 ? 1 : count($users->toArray()) / 10;
        $total_page = $total_page > (int)(count($users->toArray()) / 10) ? (int)(count($users->toArray()) / 10) + 1 : $total_page;
        $limit = 10;
        // $users = $users->sortByDesc('created_at')->skip($skip)->take($limit);
        $users = $users->sortByDesc('created_at');
        $data = ['users' => $users, 'current_page' => $page, 'total_page' => $total_page];

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
        if ($request->user_level == 'sub-agent') {
            $user = User::where('id', $request->user_id)->first();
            if (!$user->code) {
                $bytes = random_bytes(20);
                $user->code = bin2hex($bytes);
            }
            $user->user_level = $request->user_level;
            $user->save();
        } else {
            $user = User::where('id', $request->user_id)->update(['user_level' => $request->user_level]);
        }

        $user_data = User::where('id', $request->user_id)->first();

        return redirect()->back()->with('success', '' . $user_data->name . ' changed level to ' . ($request->user_level == 'sub-agent' ? 'sub-agent' : 'player'));
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
        $bytes = random_bytes(20);
        $code = bin2hex($bytes);
        $registered = User::create([
            'name' => $registration->name,
            'username' => $registration->username,
            'email' => $registration->email,
            'phone_number' => $registration->phone_number,
            'facebook_link' => $registration->facebook_link,
            'code' => $code,
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
        // if ($soft_delete) {
        //     Notification::send($registered, new Deactivated());
        // }else{
        //     Notification::send($registered, new RequestApproved());
        // }
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
            $users = User::withTrashed()->where('status', 'deactivated')->whereIn('id', $user_id)->get();
        } else {
            $users = User::withTrashed()->where('status', 'deactivated')->get();
        }


        $page = isset($request->page) ? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($users->toArray()) < 10 ? 1 : count($users->toArray()) / 10;
        $total_page = $total_page > (int)(count($users->toArray()) / 10) ? (int)(count($users->toArray()) / 10) + 1 : $total_page;
        $limit = 10;
        $users = $users->sortByDesc('created_at');
        $data = ['users' => $users, 'current_page' => $page, 'total_page' => $total_page];

        return view('agent.pages.deactivated', $data);
    }

    public function editUserInfo(Request $request)
    {
        $user_id = $request->user_id;
        $updateUser = [
            'name' => $request->name,
            'phone_number' => $request->phone_number,
        ];
        User::where('id', $request->user_id)->update($updateUser);
        return redirect()->back()->with('success', 'Saved !');
    }

    public function cleardeactivatedPlayers(Request $request)
    {
        $user = Auth::user();
        $username = $request->username ? $request->username : '';
        if ($username) {
            $user_id1 = User::withTrashed()->where('name', 'like', '%' . $username . '%')->get()->pluck('id')->toArray();
            $user_id2 = User::withTrashed()->where('username', 'like', '%' . $username . '%')->get()->pluck('id')->toArray();
            $user_id = array_merge($user_id1, $user_id2);
            $users = User::withTrashed()->where('status', 'deactivated')->whereIn('id', $user_id)->delete();
            $users = User::withTrashed()->where('status', 'deactivated')->whereIn('id', $user_id)->get();
        } else {
            $users = User::withTrashed()->where('status', 'deactivated')->delete();
            $users = User::withTrashed()->where('status', 'deactivated')->get();
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
            return redirect()->back()->with('failed', 'Request rejected !');
        } else {
            $load_to->transaction_status = 'success';
            $load_to->save();
            Wallet::where('user_id', $user->id)->update(['points' => floatval($user->wallet->points + $load_to->amount)]);
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
        $ids = config('settings.bots.agent') . ',' . config('settings.bots.meron') . ',' . config('settings.bots.wala');
        $ids = explode(",", $ids);

        $username = $request->username ? $request->username : '';
        if ($username) {
            $user_id1 = User::where('name', 'like', '%' . $username . '%')->get()->pluck('id')->toArray();
            $user_id2 = User::where('username', 'like', '%' . $username . '%')->get()->pluck('id')->toArray();
            $user_id = array_merge($user_id1, $user_id2);
            $users = User::whereIn('id', $user_id)->where('user_level', 'master-agent')->whereNotIn('id', $ids)->get();
        } else {
            $users = User::where('user_level', 'master-agent')->whereNotIn('id', $ids)->get();
        }

        $page = isset($request->page) ? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($users->toArray()) < 10 ? 1 : count($users->toArray()) / 10;
        $total_page = $total_page > (int)(count($users->toArray()) / 10) ? (int)(count($users->toArray()) / 10) + 1 : $total_page;
        $limit = 10;
        $users = $users->sortByDesc('created_at');
        $data = [
            'users' => $users,
            'current_page' => $page,
            'total_page' => $total_page
        ];

        return view('admin.agentList', $data);
    }

    public function clearagentList(Request $request)
    {
        $user = Auth::user();
        $username = $request->username ? $request->username : '';
        if ($username) {
            $user_id1 = User::where('name', 'like', '%' . $username . '%')->get()->pluck('id')->toArray();
            $user_id2 = User::where('username', 'like', '%' . $username . '%')->get()->pluck('id')->toArray();
            $user_id = array_merge($user_id1, $user_id2);
            $users = User::whereIn('id', $user_id)->where('user_level', 'master-agent')->delete();
            $users = User::whereIn('id', $user_id)->where('user_level', 'master-agent')->get();
        } else {
            $users = User::where('user_level', 'master-agent')->delete();
            $users = User::where('user_level', 'master-agent')->get();
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

    public function subAgentList(Request $request)
    {
        $user = Auth::user();
        $ids = config('settings.bots.agent') . ',' . config('settings.bots.meron') . ',' . config('settings.bots.wala');
        $ids = explode(",", $ids);

        $username = $request->username ? $request->username : '';
        if ($username) {
            $user_id1 = User::where('name', 'like', '%' . $username . '%')->get()->pluck('id')->toArray();
            $user_id2 = User::where('username', 'like', '%' . $username . '%')->get()->pluck('id')->toArray();
            $user_id = array_merge($user_id1, $user_id2);
            $users = User::whereIn('id', $user_id)->where('user_level', 'sub-agent')->whereNotIn('id', $ids)->get();
        } else {
            $users = User::where('user_level', 'sub-agent')->whereNotIn('id', $ids)->get();
        }

        $page = isset($request->page) ? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($users->toArray()) < 10 ? 1 : count($users->toArray()) / 10;
        $total_page = $total_page > (int)(count($users->toArray()) / 10) ? (int)(count($users->toArray()) / 10) + 1 : $total_page;
        $limit = 10;
        $users = $users->sortByDesc('created_at');
        $data = ['users' => $users, 'current_page' => $page, 'total_page' => $total_page, 'htitle' => 'SUB OPERATORS'];

        return view('agent.pages.subAgentList', $data);
    }

    public function goldAgentList(Request $request)
    {
        $user = Auth::user();
        $username = $request->username ? $request->username : '';
        if ($username) {
            $user_id1 = User::where('name', 'like', '%' . $username . '%')->get()->pluck('id')->toArray();
            $user_id2 = User::where('username', 'like', '%' . $username . '%')->get()->pluck('id')->toArray();
            $user_id = array_merge($user_id1, $user_id2);
            $users = User::whereIn('id', $user_id)->where('user_level', 'gold-agent')->get();
        } else {
            $users = User::where('user_level', 'gold-agent')->get();
        }

        $page = isset($request->page) ? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($users->toArray()) < 10 ? 1 : count($users->toArray()) / 10;
        $total_page = $total_page > (int)(count($users->toArray()) / 10) ? (int)(count($users->toArray()) / 10) + 1 : $total_page;
        $limit = 10;
        $users = $users->sortByDesc('created_at');
        $data = ['users' => $users, 'current_page' => $page, 'total_page' => $total_page, 'htitle' => 'MASTER AGENTS'];

        return view('agent.pages.subAgentList', $data);
    }

    public function silverAgentList(Request $request)
    {
        $user = Auth::user();
        $username = $request->username ? $request->username : '';
        if ($username) {
            $user_id1 = User::where('name', 'like', '%' . $username . '%')->get()->pluck('id')->toArray();
            $user_id2 = User::where('username', 'like', '%' . $username . '%')->get()->pluck('id')->toArray();
            $user_id = array_merge($user_id1, $user_id2);
            $users = User::whereIn('id', $user_id)->where('user_level', 'silver-agent')->get();
        } else {
            $users = User::where('user_level', 'silver-agent')->get();
        }

        $page = isset($request->page) ? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($users->toArray()) < 10 ? 1 : count($users->toArray()) / 10;
        $total_page = $total_page > (int)(count($users->toArray()) / 10) ? (int)(count($users->toArray()) / 10) + 1 : $total_page;
        $limit = 10;
        $users = $users->sortByDesc('created_at');
        $data = ['users' => $users, 'current_page' => $page, 'total_page' => $total_page, 'htitle' => 'GOLD AGENTS'];

        return view('agent.pages.subAgentList', $data);
    }

    public function bronzeAgentList(Request $request)
    {
        $user = Auth::user();
        $username = $request->username ? $request->username : '';
        if ($username) {
            $user_id1 = User::where('name', 'like', '%' . $username . '%')->get()->pluck('id')->toArray();
            $user_id2 = User::where('username', 'like', '%' . $username . '%')->get()->pluck('id')->toArray();
            $user_id = array_merge($user_id1, $user_id2);
            $users = User::whereIn('id', $user_id)->where('user_level', 'bronze-agent')->get();
        } else {
            $users = User::where('user_level', 'bronze-agent')->get();
        }

        $page = isset($request->page) ? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($users->toArray()) < 10 ? 1 : count($users->toArray()) / 10;
        $total_page = $total_page > (int)(count($users->toArray()) / 10) ? (int)(count($users->toArray()) / 10) + 1 : $total_page;
        $limit = 10;
        $users = $users->sortByDesc('created_at');
        $data = ['users' => $users, 'current_page' => $page, 'total_page' => $total_page];

        return view('agent.pages.subAgentList', $data);
    }

    public function clearsubAgentList(Request $request)
    {
        $user = Auth::user();
        $username = $request->username ? $request->username : '';
        if ($username) {
            $user_id1 = User::where('name', 'like', '%' . $username . '%')->get()->pluck('id')->toArray();
            $user_id2 = User::where('username', 'like', '%' . $username . '%')->get()->pluck('id')->toArray();
            $user_id = array_merge($user_id1, $user_id2);
            $users = User::whereIn('id', $user_id)->where('user_level', 'sub-agent')->delete();
            $users = User::whereIn('id', $user_id)->where('user_level', 'sub-agent')->get();
        } else {
            $users = User::where('user_level', 'sub-agent')->delete();
            $users = User::where('user_level', 'sub-agent')->get();
        }

        $page = isset($request->page) ? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($users->toArray()) < 10 ? 1 : count($users->toArray()) / 10;
        $total_page = $total_page > (int)(count($users->toArray()) / 10) ? (int)(count($users->toArray()) / 10) + 1 : $total_page;
        $limit = 10;
        // $users = $users->sortByDesc('created_at')->skip($skip)->take($limit);
        $users = $users->sortByDesc('created_at');
        $data = ['users' => $users, 'current_page' => $page, 'total_page' => $total_page];

        return view('agent.pages.subAgentList', $data);
    }

    public function siteSettings(Request $request)
    {
        $siteSettings = SiteSettings::where('name', 'LIKE', '%video%')->orWhere('name', 'LIKE', '%Video%')->get();
        // dd($siteSettings);
        return view('agent.pages.siteSettings', ['siteSettings' => $siteSettings]);
    }

    public function siteSettingsSave(Request $request)
    {
        if (!empty($request->upload_type) && $request->upload_type == 1) {
            $rules = [
                'value' => 'mimes:jpeg,bmp,png,jpg,gif'
            ];
            $x = $request->all();
            $validator = Validator::make($x, $rules);
            $validator->validate();

            $file = request()->file('value');
            $file_name = $file->hashName();
            $file->store('image', ['disk' => 'public_uploads']);
            /*echo '<pre>';
            var_dump($path);
            var_dump($_FILES);
            var_dump($file);
            var_dump($file_name);
            die();//*/

            $siteSettings = SiteSettings::where('id', $request->id)->first();
            $siteSettings->value = 'image/' . $file_name;
            $siteSettings->save();
        } else {
            if (isset($request->bet)) {
                Validator::make($request->all(), [
                    'bot_min_bet' => ['required', 'numeric', 'lt:bot_max_bet'],
                    'bot_max_bet' => ['required', 'numeric', 'gt:bot_min_bet'],
                ])->validate();
                $siteSettings_min = SiteSettings::where('id', 3)->first();
                $siteSettings_min->value = $request->bot_min_bet;
                $siteSettings_min->save();

                $siteSettings_max = SiteSettings::where('id', 4)->first();
                $siteSettings_max->value = $request->bot_max_bet;
                $siteSettings_max->save();
            } else {
                $siteSettings = SiteSettings::where('id', $request->id)->first();
                $siteSettings->value = $request->value;
                $siteSettings->save();
            }
        }

        return redirect()->back()->with('success', 'Saved !');
    }

    public function uploadVideo(Request $request)
    {


        try {

            $file = $request->file('video');

            if (!$file || $file->getSize() < 1) {
                return redirect()->back()->with('error', "Rejected");
            }

            $fileExtension = $file->getClientOriginalExtension();

            if ($fileExtension != 'mp4') {
                return redirect()->back()->with('error', "Rejected");
            }

//            if(in_array($file->getMimeType(), ['video/mp4'])) {
//                return redirect()->back()->with('error', "Pls select mp4 file");
//            }

            if (file_exists('public/betting.mp4')) {
                unlink(public_path('public/betting.mp4'));
            }

            $videoSetting = SiteSettings::where('name', "LIKE", "%" . "video" . "%")->orWhere('name', "LIKE", "%" . "Video" . "%")->first();

            if (!$videoSetting) {
                $videoSetting = new SiteSettings();
                $videoSetting->name = 'video';
                $videoSetting->value = 'public/betting.mp4';
                $videoSetting->save();
            }

            $videoSetting->value = 'public/betting.mp4';
            $videoSetting->save();

            $destinationPath = 'public';
            $file->move($destinationPath, 'betting.mp4');

            return redirect()->back()->with('success', 'Saved');

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    //
    public function modifyGame(Request $request)
    {
        $loggedInuser = Auth::user();

        if (empty($request->gr_id)) {
            return redirect()->back()->with('error', 'Redirected');
        }

        if (empty($request->action_type)) {
            return redirect()->back()->with('error', 'Redirected');
        }

        $cg = GameRound::where('id', $request->gr_id)->first();

        if (empty($cg)) {
            return redirect()->back()->with('error', 'Redirected');
        }

        if ($request->action_type == 'start') {
            if ($cg->status != 'upcoming') {
                return redirect()->back()->with('error', 'Redirected');
            }
            GameRound::where('id', $request->gr_id)->update([
                'status' => 'open'
            ]);
            // Executing queue worker only once
            /*exec('php artisan betting:meron 3 > storage/logs/betting_meron_' . date('Y_m_d') . '.log &');
            exec('php artisan betting:wala 3 > storage/logs/betting_wala_' . date('Y_m_d') . '.log &');*/

        } else if ($request->action_type == 'closed') {
            if ($cg->status != 'open') {
                return redirect()->back()->with('error', 'Redirected');
            }
            GameRound::where('id', $request->gr_id)->update([
                'status' => 'closed'
            ]);
        } else if ($request->action_type == 'cancel') {
            if (!in_array($cg->status, ['closed', 'undo', 'open'])) {
                return redirect()->back()->with('error', 'Redirected');
            }

            GameRound::where('id', $request->gr_id)->update([
                'status' => 'cancelled'
            ]);

            BettingHistory::where('game_rounds_id', $request->gr_id)
                ->update([
                    'status' => 'cancelled'
                ]);

            $allWallets = DB::table('betting_histories as b')
                ->leftJoin('wallets as a', 'a.user_id', '=', 'b.player_id')
                ->whereIn('b.status', ['cancelled'])
                ->where('b.updated_wallet', '0')
                ->where('b.game_rounds_id', $request->gr_id)
                ->select('b.game_rounds_id as game_round_id', 'a.id as wallet_id', 'b.amount as amount')
                ->get();

            foreach ($allWallets as $wallet) {

                $canceledWallet = DB::table('wallets')->where('id', $wallet->wallet_id)->first();

                if ($canceledWallet) {
                    DB::table('wallets as a')->where('a.id', $wallet->wallet_id)->update([
                        'points' => $canceledWallet->points + $wallet->amount
                    ]);
                }

            }
            DB::table('betting_histories as b')
                ->leftJoin('wallets as a', 'a.user_id', '=', 'b.player_id')
                ->whereIn('b.status', ['cancelled'])
                ->where('b.updated_wallet', '0')
                ->where('b.game_rounds_id', $request->gr_id)
                ->update([
                    'b.updated_wallet' => '1',
                    'a.updated_at' => DB::raw("CURRENT_TIMESTAMP"),
                    'b.updated_at' => DB::raw("CURRENT_TIMESTAMP")
                ]);

        } else if ($request->action_type == 'draw') {

            if (!in_array($cg->status, ['closed', 'undo'])) {
                return redirect()->back()->with('error', 'Redirected');
            }

            GameRound::where('id', $request->gr_id)->update([
                'status' => 'draw',
                'winner' => 'draw'
            ]);

            BettingHistory::where('game_rounds_id', $request->gr_id)
                ->update([
                    'status' => 'draw'
                ]);

            $allWallets = DB::table('betting_histories as b')
                ->leftJoin('wallets as a', 'a.user_id', '=', 'b.player_id')
                ->whereIn('b.status', ['draw'])
                ->where('b.updated_wallet', '0')
                ->where('b.game_rounds_id', $request->gr_id)
                ->select('b.game_rounds_id as game_round_id', 'a.id as wallet_id', 'b.amount as amount', 'b.player_id')
                ->get();

            foreach ($allWallets as $wallet) {

                Winner::create([
                    'game_rounds_id' => $request->gr_id,
                    'winner' => "draw",
                    'amount' => 0,
                    'player_id' => $wallet->player_id,
                ]);

                $canceledWallet = DB::table('wallets')->where('id', $wallet->wallet_id)->first();

                if ($canceledWallet) {
                    DB::table('wallets as a')->where('a.id', $wallet->wallet_id)->update([
                        'points' => $canceledWallet->points + $wallet->amount
                    ]);
                }

            }
            DB::table('betting_histories as b')
                ->leftJoin('wallets as a', 'a.user_id', '=', 'b.player_id')
                ->whereIn('b.status', ['draw'])
                ->where('b.updated_wallet', '0')
                ->where('b.game_rounds_id', $request->gr_id)
                ->update([
                    'b.updated_wallet' => '1',
                    'a.updated_at' => DB::raw("CURRENT_TIMESTAMP"),
                    'b.updated_at' => DB::raw("CURRENT_TIMESTAMP")
                ]);

        } else if ($request->action_type == 'hide') {
            if ($cg->status != 'closed') {
                return redirect()->back()->with('error', 'Redirected');
            }
            GameRound::where('id', $request->gr_id)->update([
                'status' => 'done'
            ]);
        } else if ($request->action_type == 'update-winner') {
            if (!in_array($cg->status, ['closed', 'undo'])) {
                return redirect()->back()->with('error', 'Redirected');
            }

            if (in_array($cg->winner, ['heads', 'tails'])) {
                return redirect()->back()->with('error', 'Redirected');
            }

            if (empty($request->winner)) {
                return redirect()->back()->with('success', 'Saved !');
            }

            GameRound::where('id', $request->gr_id)->update([
                'winner' => $request->winner
            ]);

            $loser_side = '';
            $winner_value = 0;
            $lose_value = 0;
            $heads = BettingHistory::where("game_rounds_id", $request->gr_id)->where("side", "heads")->sum('amount');
            $tails = BettingHistory::where("game_rounds_id", $request->gr_id)->where("side", "tails")->sum('amount');

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

            if ($request->winner == 'heads') {
                $loser_side = 'tails';
                $winner_value = $payout_heads;
                $lose_value = $payout_tails;
            } else if ($request->winner == 'tails') {
                $loser_side = 'heads';
                $winner_value = $payout_tails;
                $lose_value = $payout_heads;
            }

            BettingHistory::where('game_rounds_id', $request->gr_id)
                ->where('side', $request->winner)
                ->where('status', 'ongoing')
                ->update([
                    'win_amount' => DB::raw('(amount / 100 * ' . $winner_value . ')'),
                    'status' => 'win'
                ]);

            BettingHistory::where('game_rounds_id', $request->gr_id)
                ->where('side', $loser_side)
                ->where('status', 'ongoing')
                ->update([
                    'loose_amount' => DB::raw('amount'),
                    'status' => 'loose',
                    'updated_wallet' => '1'
                ]);

            DB::table('wallets as a')
                ->join('betting_histories as b', 'a.user_id', '=', 'b.player_id')
                ->whereIn('b.status', ['win'])
                ->where('b.updated_wallet', '0')
                ->where('b.game_rounds_id', $request->gr_id)
                ->update([
                    'a.points' => DB::raw("(a.points + b.win_amount)"),
                    'b.updated_wallet' => '1',
                    'a.updated_at' => DB::raw("CURRENT_TIMESTAMP"),
                    'b.updated_at' => DB::raw("CURRENT_TIMESTAMP")
                ]);

            // Compute commission
            $entries = BettingHistory::whereIn('status', ['win', 'loose'])
                ->where('commission_updated', 0)
                ->where('game_rounds_id', $request->gr_id)
                ->get();

            $winnerNotify = [];
            $game = DB::table('game_rounds')->select('round')->where('id', $request->gr_id)->get();
            foreach ($entries as $key => $entry) {

                if ($entry->status == "win") {
                    $winner = "meron wins";
                    if ($entry->side == "tails") {
                        $winner = "wala wins";
                    }
                    Winner::create([
                        'game_rounds_id' => $request->gr_id,
                        'winner' => $winner,
                        'amount' => $entry->win_amount,
                        'player_id' => $entry->player_id,
                    ]);
                }

                $wallet = Wallet::where('user_id', $entry->player_id)->first();
                $user = User::where('id', $wallet->user_id)->first();
                $agent = User::where('code', $user->referral_id)->first();
                $admin = User::where('username', $loggedInuser->username)->first();

                /****************************************Agent Commission Start*********************************************/
                if ($agent->user_level == 'master-agent') {
                    $Mcomm = Wallet::where('user_id', $agent->id)->first();
                    $Mcommission = config('settings.commission.master');
                    if ($agent->commission_percent > 0) {
                        $Mcommission = ($agent->commission_percent / 100);
                    }
                    $added = $Mcomm->update([
                        'comission' => $Mcomm->comission + ($entry->amount * $Mcommission)
                    ]);
                    if ($added) {
                        $comLogs = Commission::create([
                            "round" => $game[0]->round,
                            "from" => $entry->player_id,
                            "to" => $agent->id,
                            "amount" => ($entry->amount * $Mcommission),
                            "commission_percentage" => (100 * $Mcommission),
                            "details" => "Direct Player commission. game round id #" . $request->gr_id
                        ]);
                    }
                } elseif ($agent->user_level == 'sub-agent') {
                    $SA = Wallet::where('user_id', $agent->id)->first();
                    $SAcommission = config('settings.commission.sub');
                    if ($agent->commission_percent > 0) {
                        $SAcommission = ($agent->commission_percent / 100);
                    }
                    $added = $SA->update([
                        'comission' => $SA->comission + ($entry->amount * $SAcommission)
                    ]);
                    if ($added) {
                        $comLogs = Commission::create([
                            "round" => $game[0]->round,
                            "from" => $entry->player_id,
                            "to" => $agent->id,
                            "amount" => ($entry->amount * $SAcommission),
                            "commission_percentage" => (100 * $SAcommission),
                            "details" => "Sub agent commission from Direct player. game round id #" . $request->gr_id
                        ]);
                    }

                    $MA = User::where('code', $agent->referral_id)->first();
                    $Mcommission = config('settings.commission.master') - config('settings.commission.sub');
                    if ($MA->commission_percent > 0) {
                        $Mcommission = (($MA->commission_percent - $agent->commission_percent) / 100);
                    }
                    $MAcomm = Wallet::where('user_id', $MA->id)->first();
                    $added = $MAcomm->update([
                        'comission' => $MAcomm->comission + ($entry->amount * $Mcommission)
                    ]);
                    if ($added) {
                        $comLogs = Commission::create([
                            "round" => $game[0]->round,
                            "from" => $entry->player_id,
                            "to" => $MA->id,
                            "amount" => ($entry->amount * $Mcommission),
                            "commission_percentage" => (100 * $Mcommission),
                            "details" => "Master agent commission from sub agent player. game round id #" . $request->gr_id
                        ]);
                    }
                } elseif ($agent->user_level == 'gold-agent') {
                    $SA = Wallet::where('user_id', $agent->id)->first();
                    $SAcommission = config('settings.commission.gold');
                    if ($agent->commission_percent > 0) {
                        $SAcommission = ($agent->commission_percent / 100);
                    }
                    $added = $SA->update([
                        'comission' => $SA->comission + ($entry->amount * $SAcommission)
                    ]);
                    if ($added) {
                        $comLogs = Commission::create([
                            "round" => $game[0]->round,
                            "from" => $entry->player_id,
                            "to" => $agent->id,
                            "amount" => ($entry->amount * $SAcommission),
                            "commission_percentage" => (100 * $SAcommission),
                            "details" => "Gold agent Commission from direct player. game round id #" . $request->gr_id
                        ]);
                    }

                    $MA = User::where('code', $agent->referral_id)->first();
                    $Mcommission = config('settings.commission.sub') - config('settings.commission.gold');
                    if ($MA->commission_percent > 0) {
                        $Mcommission = (($MA->commission_percent - $agent->commission_percent) / 100);
                    }
                    $MAcomm = Wallet::where('user_id', $MA->id)->first();
                    $added = $MAcomm->update([
                        'comission' => $MAcomm->comission + ($entry->amount * $Mcommission)
                    ]);
                    if ($added) {
                        $comLogs = Commission::create([
                            "round" => $game[0]->round,
                            "from" => $entry->player_id,
                            "to" => $MA->id,
                            "amount" => ($entry->amount * $Mcommission),
                            "commission_percentage" => (100 * $Mcommission),
                            "details" => "Sub agent commission from gold agent player. game round id #" . $request->gr_id
                        ]);
                    }

                    $MA = User::where('code', $MA->referral_id)->first();
                    $Mcommission = config('settings.commission.master') - config('settings.commission.sub');
                    if ($MA->commission_percent > 0) {
                        $Mcommission = (($MA->commission_percent - $agent->commission_percent) / 100);
                    }
                    $MAcomm = Wallet::where('user_id', $MA->id)->first();
                    $added = $MAcomm->update([
                        'comission' => $MAcomm->comission + ($entry->amount * $Mcommission)
                    ]);
                    if ($added) {
                        $comLogs = Commission::create([
                            "round" => $game[0]->round,
                            "from" => $entry->player_id,
                            "to" => $MA->id,
                            "amount" => ($entry->amount * $Mcommission),
                            "commission_percentage" => (100 * $Mcommission),
                            "details" => "Master agent commission from gold agent player. game round id #" . $request->gr_id
                        ]);
                    }
                } elseif ($agent->user_level == 'silver-agent') {
                    $SA = Wallet::where('user_id', $agent->id)->first();
                    $SAcommission = config('settings.commission.silver');
                    if ($agent->commission_percent > 0) {
                        $SAcommission = ($agent->commission_percent / 100);
                    }
                    $added = $SA->update([
                        'comission' => $SA->comission + ($entry->amount * $SAcommission)
                    ]);
                    if ($added) {
                        $comLogs = Commission::create([
                            "round" => $game[0]->round,
                            "from" => $entry->player_id,
                            "to" => $agent->id,
                            "amount" => ($entry->amount * $SAcommission),
                            "commission_percentage" => (100 * $SAcommission),
                            "details" => "Silver agent Commission from direct player. game round id #" . $request->gr_id
                        ]);
                    }

                    $MA = User::where('code', $agent->referral_id)->first();
                    $Mcommission = config('settings.commission.gold') - config('settings.commission.silver');
                    if ($MA->commission_percent > 0) {
                        $Mcommission = (($MA->commission_percent - $agent->commission_percent) / 100);
                    }
                    $MAcomm = Wallet::where('user_id', $MA->id)->first();
                    $added = $MAcomm->update([
                        'comission' => $MAcomm->comission + ($entry->amount * $Mcommission)
                    ]);
                    if ($added) {
                        $comLogs = Commission::create([
                            "round" => $game[0]->round,
                            "from" => $entry->player_id,
                            "to" => $MA->id,
                            "amount" => ($entry->amount * $Mcommission),
                            "commission_percentage" => (100 * $Mcommission),
                            "details" => "Gold agent commission from silver agent player. game round id #" . $request->gr_id
                        ]);
                    }

                    $MA = User::where('code', $MA->referral_id)->first();
                    $Mcommission = config('settings.commission.sub') - config('settings.commission.gold');
                    if ($MA->commission_percent > 0) {
                        $Mcommission = (($MA->commission_percent - $agent->commission_percent) / 100);
                    }
                    $MAcomm = Wallet::where('user_id', $MA->id)->first();
                    $added = $MAcomm->update([
                        'comission' => $MAcomm->comission + ($entry->amount * $Mcommission)
                    ]);
                    if ($added) {
                        $comLogs = Commission::create([
                            "round" => $game[0]->round,
                            "from" => $entry->player_id,
                            "to" => $MA->id,
                            "amount" => ($entry->amount * $Mcommission),
                            "commission_percentage" => (100 * $Mcommission),
                            "details" => "Sub agent commission from silver agent player. game round id #" . $request->gr_id
                        ]);
                    }
                    $MA = User::where('code', $MA->referral_id)->first();
                    $Mcommission = config('settings.commission.master') - config('settings.commission.sub');
                    if ($MA->commission_percent > 0) {
                        $Mcommission = (($MA->commission_percent - $agent->commission_percent) / 100);
                    }
                    $MAcomm = Wallet::where('user_id', $MA->id)->first();
                    $added = $MAcomm->update([
                        'comission' => $MAcomm->comission + ($entry->amount * $Mcommission)
                    ]);
                    if ($added) {
                        $comLogs = Commission::create([
                            "round" => $game[0]->round,
                            "from" => $entry->player_id,
                            "to" => $MA->id,
                            "amount" => ($entry->amount * $Mcommission),
                            "commission_percentage" => (100 * $Mcommission),
                            "details" => "Master agent commission from silver agent player. game round id #" . $request->gr_id
                        ]);
                    }
                }

                /****************************************Agent Commission End*********************************************/
                //Admin Commission
                $Acom = Wallet::where('user_id', $admin->id)->first();
                if (!is_null($Acom)) {
                    $com = config('settings.commission.admin');
                    if ($admin->commission_percent > 0) {
                        $com = ($admin->commission_percent / 100);
                    }
                    $added = $Acom->update([
                        'comission' => $Acom->comission + ($entry->amount * $com)
                    ]);
                    if ($added) {
                        $comLogs = Commission::create([
                            "round" => $game[0]->round,
                            "from" => $entry->player_id,
                            "to" => $admin->id,
                            "amount" => ($entry->amount * $com),
                            "commission_percentage" => (100 * $com),
                            "details" => "This added on winner declare for admin {$admin->username}. game round id #" . $request->gr_id
                        ]);
                    }
                }
            }

            BettingHistory::where('game_rounds_id', $request->gr_id)
                ->update([
                    'updated_wallet' => 1,
                    'commission_updated' => 1
                ]);
            //ENDBYMIKE

        } else if ($request->action_type == 'undo') {

            $entries = BettingHistory::whereIn('status', ['win', 'loose'])
                ->where([
                    ['updated_wallet', 1],
                    ['commission_updated', 1]
                ])
                ->where('game_rounds_id', $request->gr_id)
                ->get();
            $game = DB::table('game_rounds')->select('round')->where('id', $request->gr_id)->get();
            foreach ($entries as $key => $entry) {
                $wallet = Wallet::where('user_id', $entry->player_id)->first();
                $user = User::where('id', $wallet->user_id)->first();
                $agent = User::where('code', $user->referral_id)->first();
                $admin = User::where('username', $loggedInuser->username)->first();

                $wallet->update([
                    'points' => $wallet->points - $entry->win_amount
                ]);

                if ($agent->user_level == 'master-agent') {
                    $Mcomm = Wallet::where('user_id', $agent->id)->first();
                    $Mcommission = config('settings.commission.master');
                    if ($agent->commission_percent > 0) {
                        $Mcommission = ($agent->commission_percent / 100);
                    }
                    $updated = $Mcomm->update([
                        'comission' => $Mcomm->comission - ($entry->amount * $Mcommission)
                    ]);
                    if ($updated) {
                        $comLogs = Commission::create([
                            "round" => $game[0]->round,
                            "from" => $entry->player_id,
                            "to" => $agent->id,
                            "amount" => -($entry->amount * $Mcommission),
                            "commission_percentage" => (100 * $Mcommission),
                            "details" => "Admin undo game result. game round id #" . $request->gr_id
                        ]);
                    }
                } elseif ($agent->user_level == 'sub-agent') {
                    $SA = Wallet::where('user_id', $agent->id)->first();
                    $SAcommission = config('settings.commission.sub');
                    if ($agent->commission_percent > 0) {
                        $SAcommission = ($agent->commission_percent / 100);
                    }
                    $updated = $SA->update([
                        'comission' => $SA->comission - ($entry->amount * $SAcommission)
                    ]);
                    if ($updated) {
                        $comLogs = Commission::create([
                            "round" => $game[0]->round,
                            "from" => $entry->player_id,
                            "to" => $agent->id,
                            "amount" => -($entry->amount * $SAcommission),
                            "commission_percentage" => (100 * $SAcommission),
                            "details" => "Admin undo game result. game round id #" . $request->gr_id
                        ]);
                    }
                    $MA = User::where('code', $agent->referral_id)->first();
                    $Mcommission = config('settings.commission.master') - config('settings.commission.sub');
                    if ($MA->commission_percent > 0) {
                        $Mcommission = (($MA->commission_percent - $agent->commission_percent) / 100);
                    }
                    $MAcomm = Wallet::where('user_id', $MA->id)->first();
                    $updated = $MAcomm->update([
                        'comission' => $MAcomm->comission - ($entry->amount * $Mcommission)
                    ]);
                    if ($updated) {
                        $comLogs = Commission::create([
                            "round" => $game[0]->round,
                            "from" => $entry->player_id,
                            "to" => $MA->id,
                            "amount" => -($entry->amount * $Mcommission),
                            "commission_percentage" => (100 * $Mcommission),
                            "details" => "Admin undo game result. game round id #" . $request->gr_id
                        ]);
                    }
                }
                $Acom = Wallet::where('user_id', $admin->id)->first();
                if (!is_null($Acom)) {
                    $com = config('settings.commission.admin');
                    $updated = $Acom->update([
                        'comission' => $Acom->comission - ($entry->amount * $com)
                    ]);
                    if ($updated) {
                        $comLogs = Commission::create([
                            "round" => $game[0]->round,
                            "from" => $entry->player_id,
                            "to" => $admin->id,
                            "amount" => -($entry->amount * $com),
                            "commission_percentage" => (100 * $com),
                            "details" => "Admin undo game result. game round id #" . $request->gr_id
                        ]);
                    }
                }
            }

            GameRound::where('id', $request->gr_id)->update([
                'status' => 'undo',
                'winner' => 'none'
            ]);

            BettingHistory::where('game_rounds_id', $request->gr_id)
                ->update([
                    'status' => 'ongoing',
                    'updated_wallet' => 0,
                    'commission_updated' => 0,
                    'win_amount' => 0,
                    'loose_amount' => 0,
                ]);
        }
        return redirect()->back()->with('success', 'Saved !');
    }

    public function addGame(Request $request)
    {
        if (empty($request->Game_color)) {
            return redirect()->back()->with('error', 'Invalid Input');
        }
        if (empty($request->game_round_number) || !$request->game_round_number || $request->game_round_number < 1 || !is_numeric($request->game_round_number)) {
            return redirect()->back()->with('error', 'Invalid Game Round');
        }
        $time = date('Y-m-d', strtotime(Carbon::now()));
        $cg = GameRound::whereIn('status', ['upcoming', 'open', 'ongoing', 'final-bet', 'closed'])->where('created_at', 'like', $time . '%')->orderBy('id', 'DESC')->first();
        $gr = new GameRound;
        $gr->status = 'upcoming';
        $gr->meron = 'meron';
        $gr->wala = 'wala';
        $gr->Game_color = $request->Game_color;
        $gr->round = $request->game_round_number;
        $gr->save();
        return redirect()->back()->with('success', 'Saved !');
    }

    public function editCommission(Request $request)
    {
        $user = Auth::user();
        $agents = User::whereIn('user_level', ['master-agent'])->where('status', 'activated')->get();
        $maxCom = ($user->commission_percent > 0) ? $user->commission_percent : (config('settings.commission.admin') * 100);
        $minCom = config('settings.commission.min.master');
        $data = [
            'agents' => $agents,
            'user' => $user,
            'updateUrl' => route('admin-update-comission'),
            'max_commission' => ($maxCom > 0) ? $maxCom : config('settings.commission.max.master'),
            'min_commission' => $minCom,
            'dispay_name' => config('settings.role.display'),
        ];
        return view('agent.pages.comissionEdit', $data);
    }

    public function updateCommission(Request $request, $commission, $user_id)
    {
        $user = Auth::user();
        $maxCom = ($user->commission_percent > 0) ? $user->commission_percent : (config('settings.commission.admin') * 100);
        $maxCom = ($maxCom > 0) ? $maxCom : config('settings.commission.max.master');
        $minCom = config('settings.commission.min.master');

        if ($commission >= $minCom && $commission <= $maxCom) {
            $user = User::find($user_id);
            if ($user->user_level == "master-agent") {
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

    public function showHistory(Request $request, $user_id)
    {
//        DB::enableQueryLog();
        $transLog = DB::table('transactions as t')
            ->leftJoin('point_history as ph', 'ph.tid', '=', 't.id')
            ->where([
                ['t.user_to', '=', $user_id],
                ['ph.user_id', '=', $user_id]
            ])
            ->orWhere([
                ['t.user_from', '=', $user_id],
                ['ph.user_id', '=', $user_id]
            ])
            ->select('t.id as tid', 't.amount', 't.transaction_type AS type', 't.transaction_status', DB::raw('IFNULL(ph.points, 0) AS points'), 't.created_at AS datetime')
            ->orderByDesc('datetime')
            ->get();
        //dd($transLog);
        $betLog = DB::table('betting_histories as b')
            ->leftJoin('game_rounds AS g', 'g.id', '=', 'b.game_rounds_id')
            ->where('player_id', $user_id)
            ->whereIn('b.status', ['win', 'loose', 'draw'])
            ->select('b.id AS bid', 'g.id AS gid', 'b.amount', 'b.win_amount', 'b.loose_amount', 'b.current_points as points', 'b.status AS type',
                'b.created_at AS datetime', 'g.round')
            ->orderByDesc('datetime')
            ->get();
        //dd($betLog);

        $betArcLog = DB::table('betting_archive as ba')
            ->leftJoin('game_archive AS g', 'g.id', '=', 'ba.game_rounds_id')
            ->where('player_id', $user_id)
            ->whereIn('ba.status', ['win', 'loose', 'draw'])
            ->select('ba.id AS bid', 'g.id AS gid', 'ba.amount', 'ba.win_amount', 'ba.loose_amount', 'ba.current_points as points', 'ba.status AS type',
                'ba.created_at AS datetime', 'g.round')
            ->orderByDesc('datetime')
            ->get();
        //dd($betLog);

        $logs = $transLog->merge($betLog)->merge($betArcLog)->sortBy('datetime', SORT_DESC, true);
        $userInfo = User::find($user_id);
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
                "agent-deposit" => "Cash-In",
                "agent-withdraw" => "Withdraw",
                "system-deposit" => "Cash-In",
                "system-withdraw" => "Withdraw",
            ]
        ];
        return view('agent.pages.playerHistory', $data);
    }

    public function agentHistory(Request $request, $user_id)
    {
        $logs = DB::table('transactions as t')
            ->leftJoin('point_history as ph', 'ph.tid', '=', 't.id')
            ->leftJoin('users as uf', 'uf.id', '=', 't.user_from')
            ->leftJoin('users as ut', 'ut.id', '=', 't.user_to')
            ->where([
                ['t.user_to', '=', $user_id],
                ['ph.user_id', '=', $user_id]
            ])
            ->orWhere([
                ['t.user_from', '=', $user_id],
                ['ph.user_id', '=', $user_id]
            ])
            ->select('t.id as tid', 't.amount', 't.transaction_type AS type', 't.transaction_status AS status',
                DB::raw('IFNULL(ph.points, 0) AS points'), 't.created_at AS datetime', 't.details', 'uf.name AS from', 'ut.name AS to')
            ->orderByDesc('datetime')
            ->get();
        $userInfo = User::find($user_id);
//        dd($logs);
        $data = [
            "logs" => $logs,
            "user" => $userInfo,
            "type" => [
                'deposit' => "Cash-In",
                'withdraw' => "Withdraw",
                "agent-deposit" => "Cash-In",
                "agent-withdraw" => "Withdraw",
                "system-deposit" => "Cash-In",
                "system-withdraw" => "Withdraw",
            ]
        ];
        return view('agent.pages.history', $data);
    }

    public function resetPassword(Request $request)
    {
        $updateUser = [
            'password' => Hash::make($request->password),
        ];
        $updated = User::where('id', $request->user_id)->update($updateUser);
        return redirect()->back()->with('success', 'Saved !');
    }

    public function resetGame(Request $request)
    {
        DB::insert('INSERT INTO `game_archive` (`total_bet`, `total_bet_heads`, `total_bet_tails`, `head_payout`, `tails_payout`, `round`, `coin1`, `coin2`, `wala`, `meron`, `Game_color`, `winner`, `status`, `created_at`, `updated_at`)
         SELECT `total_bet`, `total_bet_heads`, `total_bet_tails`, `head_payout`, `tails_payout`, `round`, `coin1`, `coin2`, `wala`, `meron`, `Game_color`, `winner`, `status`, `created_at`, `updated_at` FROM game_rounds;');

        /*DB::insert('INSERT INTO `commission_archive` (`round`, `from`, `to`, `amount`, `commission_percentage`, `details`, `created_at`, `updated_at`)
        SELECT `round`, `from`, `to`, `amount`, `commission_percentage`, `details`, `created_at`, `updated_at` FROM commissions;');*/

        DB::insert('INSERT INTO `betting_archive` (`game_rounds_id`, `player_id`, `amount`, `win_amount`, `loose_amount`, `current_points`, `coin1`, `coin2`, `side`, `status`, `created_at`, `updated_at`)
         SELECT `game_rounds_id`, `player_id`, `amount`, `win_amount`, `loose_amount`, `current_points`, `coin1`, `coin2`, `side`, `status`, `created_at`, `updated_at` FROM betting_histories;');

        /*DB::insert('INSERT INTO `winner_archive` (`game_rounds_id`,`winner`,`amount`,`player_id`, `created_at`, `updated_at`)
        SELECT `game_rounds_id`,`winner`,`amount`,`player_id`, `created_at`, `updated_at` FROM winner_notify;');*/

        DB::table('game_rounds')->delete();
        DB::table('betting_histories')->delete();
        //DB::table('winner_notify')->truncate();
        //DB::table('commissions')->truncate();
        return response()->json([
            "status" => true,
            "message" => "Reset Successfully"
        ]);
    }

    public function liveBetting(Request $request)
    {
        $game_rounds = GameRound::whereIn('status', ['open', 'upcoming', 'ongoing', 'final-bet', 'closed', 'done', 'cancelled', 'draw', 'undo'])
            ->whereRaw('created_at >= CURRENT_DATE')
            ->orderBy('updated_at', 'DESC')->get();
        $data = ['game_rounds' => $game_rounds];
        return view('admin.betting.live', $data);
    }
}
