<?php

namespace App\Http\Controllers;

use App\Http\Requests\WalletRequest;
use App\Models\PointHistory;
use App\Models\SiteSettings;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SuperAdminController extends Controller
{
    public function siteSettings(Request $request)
    {
        $siteSettings = SiteSettings::where('name', 'LIKE', '%video%')->orWhere('name', 'LIKE', '%Video%')->get();
        return view('superAdmin.pages.siteSettings', ['siteSettings' => $siteSettings]);
    }

    public function siteSettingsSave(Request $request)
    {
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

        return redirect()->back()->with('success', 'Saved !');
    }

    public function arciveGame(Request $request)
    {
        return view('superAdmin.archive.game');
    }

    public function games(Request $request)
    {
        try {
//            DB::enableQueryLog();
            $start = $request->get('start') ? $request->get('start') : 0;
            $limit = $request->get('length') ? $request->get('length') : 10;
            $search = $request->search["value"];
            $draw = $request->get('draw');
            $query = $request->get('query') ? $request->get('query') : '';
            $column = $request->get('column') ? $request->get('column') : 'g.created_at';
            $order = $request->get('order') ? $request->get('order') : 'DESC';

            if (isset($request->order[0])) {
                $column = $request->columns[$request->order[0]['column']]['data'];
                $order = $request->order[0]['dir'];
            }

            $qry = DB::table('game_archive as g');

            if (!is_null($start)) {
                $qry->skip($start);
            }
            if (!is_null($limit)) {
                $qry->take($limit);
            }
            if (!empty($query)) {
                $query = explode('/', $query);
                $qry->where('m.' . $query[0], $query[1], $query[2]);
            }

            if (!empty($search) && !is_null($search)) {
                if (is_numeric($search)) {
                    $qry->orWhere('g.total_bet_heads', 'like', "%" . $search . "%")
                        ->orWhere('g.total_bet_tails', 'like', "%" . $search . "%")
                        ->orWhere('g.head_payout', 'like', "%" . $search . "%")
                        ->orWhere('g.tails_payout', 'like', "%" . $search . "%");
                }
                $qry->orWhere('g.winner', 'like', "%" . $search . "%")
                    ->orWhere('g.status', 'like', "%" . $search . "%");
            }

            $qry->orderBy($column, $order);
            $games = $qry->get();
//            dd(DB::getQueryLog());

            $count = DB::table('game_archive')->count();

            return response()->json([
                'data' => $games,
                'draw' => empty($draw) ? 1 : intval($draw),
                'recordsTotal' => $count,
                'recordsFiltered' => empty($search) ? $count : $games->count()
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), $e->getTrace());
            return response()->json([
                'data' => [],
                'error' => $e->getMessage(),
                'draw' => empty($draw) ? 1 : intval($draw),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
            ]);
        }
    }

    public function arciveBetting(Request $request)
    {
        return view('superAdmin.archive.betting');
    }

    public function bettings(Request $request)
    {
        try {
//            DB::enableQueryLog();
            $start = $request->get('start') ? $request->get('start') : 0;
            $limit = $request->get('length') ? $request->get('length') : 10;
            $search = $request->search["value"];
            $draw = $request->get('draw');
            $query = $request->get('query') ? $request->get('query') : '';
            $column = $request->get('column') ? $request->get('column') : 'created_at';
            $order = $request->get('order') ? $request->get('order') : 'DESC';

            if (isset($request->order[0])) {
                $column = $request->columns[$request->order[0]['column']]['data'];
                $order = $request->order[0]['dir'];
            }

            $qry = DB::table('betting_archive AS b')
                ->join('users AS u', 'u.id', '=', 'b.player_id')
                ->join('game_archive AS g', 'g.id', '=', 'b.game_rounds_id')
                ->select('b.id', 'game_rounds_id', 'player_id', 'amount', 'current_points', 'side', 'b.status', DB::raw("DATE_FORMAT(`b`.`created_at`,'%Y-%m-%d') AS created_at"), 'username', 'round');

            if (!is_null($start)) {
                $qry->skip($start);
            }
            if (!is_null($limit)) {
                $qry->take($limit);
            }
            if (!empty($query)) {
                $query = explode('/', $query);
                $qry->where('m.' . $query[0], $query[1], $query[2]);
            }

            if (!empty($search)) {
                $qry->orWhere('u.username', 'like', "%" . $search . "%")
                    ->orWhere('b.amount', 'like', "%" . $search . "%")
                    ->orWhere('g.round', 'like', "%" . $search . "%")
                    ->orWhere('b.status', 'like', "%" . $search . "%");
            }

            $qry->orderBy($column, $order);
            $bettings = $qry->get();

            $count = DB::table('betting_archive')->count();

//            dd(DB::getQueryLog());

            return response()->json([
                'data' => $bettings,
                'draw' => empty($draw) ? 1 : intval($draw),
                'recordsTotal' => $count,
                'recordsFiltered' => empty($search) ? $count : $bettings->count()
            ]);

        } catch (\Exception $e) {
            \Log::error($e->getMessage(), $e->getTrace());
            return response()->json([
                'data' => [],
                'msg' => $e->getMessage(),
                'draw' => empty($draw) ? 1 : intval($draw),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
            ]);
        }
    }

    public function arciveWinner(Request $request)
    {
        return view('superAdmin.archive.winner');
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
        $data = ['users' => $users, 'current_page' => $page, 'total_page' => $total_page, 'htitle' => 'OPERATORS'];

        return view('superAdmin.pages.agentList', $data);
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
        $data = ['users' => $users, 'current_page' => $page, 'total_page' => $total_page, 'htitle' => 'Sub Operators'];

        return view('superAdmin.pages.agentList', $data);
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
        $data = ['users' => $users, 'current_page' => $page, 'total_page' => $total_page, 'htitle' => 'Master Agents'];

        return view('superAdmin.pages.agentList', $data);
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
        $data = ['users' => $users, 'current_page' => $page, 'total_page' => $total_page, 'htitle' => 'Gold Agents'];

        return view('superAdmin.pages.agentList', $data);
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
        $data = ['users' => $users, 'current_page' => $page, 'total_page' => $total_page, 'htitle' => 'Silver Agents'];

        return view('superAdmin.pages.agentList', $data);
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
        return view('superAdmin.pages.history', $data);
    }

    public function playerHistory(Request $request, $user_id)
    {
        //DB::enableQueryLog();
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

        //dd(DB::getQueryLog());
        $userInfo = User::find($user_id);
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
        return view('superAdmin.pages.playerHistory', $data);
    }

    public function wallet($user_id)
    {
        $user = User::where('id', $user_id)->first();
        $data = ['user' => $user];
        return view('superAdmin.pages.wallet', $data);
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
                /*$wallet = Wallet::where('user_id', $request->load_to)->first();
                $wallet->points = floatval($wallet->points - $request->amount);
                $wallet->save();*/
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
                /*$wallet = Wallet::where('user_id', $request->load_to)->first();
                $wallet->points = floatval($wallet->points + $request->amount);
                $wallet->save();*/
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

    public function walletLogs(Request $request, $user_id)
    {
        $user = User::where('id', $user_id)->first();
        if ($request->transaction_type) {
            $transaction_type = $request->transaction_type ? $request->transaction_type : null;
            if ($transaction_type) {
                $from = Transaction::where('user_from', $user_id)->where('type', 'wallet')->where('transaction_type', $transaction_type);
                $logs = Transaction::where('user_to', $user_id)->where('type', 'wallet')->union($from)->where('transaction_type', $transaction_type)->get();
            }
        } else {
            $from = Transaction::where('user_from', $user_id)->where('type', 'wallet');
            $logs = Transaction::where('user_to', $user_id)->where('type', 'wallet')->union($from)->get();
        }
        $page = isset($request->page) ? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($logs->toArray()) < 10 ? 1 : count($logs->toArray()) / 10;
        $total_page = $total_page > (int)(count($logs->toArray()) / 10) ? (int)(count($logs->toArray()) / 10) + 1 : $total_page;
        $limit = 10;
        // $wallet_logs = $logs->sortByDesc('created_at')->skip($skip)->take($limit);
        $wallet_logs = $logs->sortByDesc('created_at');
        $users = $user->user_under()->where('status', 'activated')->get();

        $data = ['wallet_logs' => $wallet_logs, 'current_page' => $page, 'total_page' => $total_page, 'users' => $users];
        return view('superAdmin.pages.walletLogs', $data);
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
        return view('superAdmin.pages.activePlayers', $data);
    }

}
