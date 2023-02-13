<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\PaymentMethod;
use App\Models\SiteSettings;
use Illuminate\Http\Request;
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
use DB;
use Illuminate\Support\Facades\Hash;
ini_set('memory_limit', '-1');
class DevAdminController extends Controller
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

        return view('superAdmin.pages.summary');
    }
    public function income(Request $request)
    {
	    $user = Auth::user();

        if ($request->date_to != '' && $request->date_from != '') {
            $from = date($request->date_from);
            $to = date($request->date_to);
            $income = Transaction::select(DB::raw("sum(amount) as total,date_format(created_at , '%Y-%m-%d 00:00:00') as date"))->where('type','wallet')->whereNotIn('user_from',[3,4,5,6,7,8,9,10,11,12])->whereIn('transaction_type',['deposit','agent-deposit','system-deposit'])->groupBy(DB::raw("date_format(created_at , '%Y-%m-%d 00:00:00')"))->orderBy(DB::raw("date_format(created_at , '%Y-%m-%d 00:00:00')"),'asc')->get();
        }else{
            $income = [];
        }
        $data = ['income'=>$income];
        return view('superAdmin.home',$data);
    }
    public function wallet($user_id)
    {
        $user = User::where('id',$user_id)->first();
        $data = ['user'=>$user];
        return view('superAdmin.pages.wallet',$data);
    }
    public function wallet_modify(WalletRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();
        $load_to = User::where('id',$request->load_to)->first();

        $transaction = new Transaction;
        if($request->transaction_type == 'withdraw'){

            $transaction->user_from = $load_to->id;
            $transaction->user_to = 1;
            if ($load_to->user_level == 'master-agent-player' || $load_to->user_level == 'sub-agent-player') {
                $transaction->transaction_type =  'withdraw';
            } else if ($load_to->user_level == 'sub-agent') {
                $transaction->transaction_type =  'agent-withdraw';
            }else{
                $transaction->transaction_type =  'system-withdraw';
            }
        }else{
            $transaction->user_from = 1;
            $transaction->user_to = $load_to->id;
            if ($load_to->user_level == 'master-agent-player' || $load_to->user_level == 'sub-agent-player') {
                $transaction->transaction_type =  'deposit';
            } else if ($load_to->user_level == 'sub-agent') {
                $transaction->transaction_type =  'agent-deposit';
            }else{
                $transaction->transaction_type =  'system-deposit';
            }
        }


        $transaction->amount = $request->amount;
        $transaction->details = $request->details;
        $transaction->type = 'wallet';

        $transaction->transaction_status = 'success';
        if($transaction->save()){
            if ($request->transaction_type == 'withdraw') {
                $wallet =  Wallet::where('user_id',$request->load_to)->first();
                $wallet->points = floatval($wallet->points - $request->amount);
                $wallet->save();
                // Wallet::where('user_id',$request->load_to)->update(['points'=>floatval($load_to->wallet->points - $request->amount)]);
                return redirect()->back()->with('success', 'withdraw success');
            }else{
                $wallet =  Wallet::where('user_id',$request->load_to)->first();
                $wallet->points = floatval($wallet->points + $request->amount);
                $wallet->save();
                // Wallet::where('user_id',$request->load_to)->update(['points'=>floatval($load_to->wallet->points + $request->amount)]);
                // Wallet::where('user_id',$user->id)->update(['points'=>floatval($user->wallet->points - $request->amount)]);
                return redirect()->back()->with('success', 'deposit success');
            }
        }
    }
    public function walletLogs(Request $request,$user_id)
    {
        $user = User::where('id',$user_id)->first();
        if ($request->transaction_type) {
            $transaction_type = $request->transaction_type?$request->transaction_type:null;
            if($transaction_type){
                $from = Transaction::where('user_from',$user_id)->where('type','wallet')->where('transaction_type',$transaction_type);
                $logs = Transaction::where('user_to',$user_id)->where('type','wallet')->union($from)->where('transaction_type',$transaction_type)->get();
            }
        }else{
            $from = Transaction::where('user_from',$user_id)->where('type','wallet');
            $logs = Transaction::where('user_to',$user_id)->where('type','wallet')->union($from)->get();
        }
        $page = isset($request->page)? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($logs->toArray()) < 10 ? 1 :  count($logs->toArray()) / 10;
        $total_page = $total_page > (int) (count($logs->toArray()) / 10) ? (int) (count($logs->toArray()) / 10) + 1:$total_page;
        $limit = 10;
        // $wallet_logs = $logs->sortByDesc('created_at')->skip($skip)->take($limit);
        $wallet_logs = $logs->sortByDesc('created_at');
        $users = $user->user_under()->where('status','activated')->get();

        $data = ['wallet_logs'=>$wallet_logs,'current_page'=>$page,'total_page'=>$total_page,'users'=>$users];
        return view('superAdmin.pages.walletLogs',$data);
    }
    public function comission()
    {
        $user = User::where('id',$user_id)->first();
        $data = ['user'=>$user];
        return view('superAdmin.pages.comission',$data);
    }
    public function comission_modify(ComissionRequest $request)
    {
        $validated = $request->validated();

        $user = Auth::user();
        $load_to = User::where('id',$request->load_to)->first();
        $transaction = new Transaction;
        if($request->transaction_type == 'withdraw'){

            $transaction->user_from = $load_to->id;
            $transaction->user_to = $user->id;
        }else{
            $transaction->user_from = $user->id;
            $transaction->user_to = $load_to->id;
        }
        $transaction->amount = $request->amount;
        $transaction->details = $request->details;
        $transaction->type = 'comission';
        $transaction->transaction_type =  'system-'.$request->transaction_type;
        $transaction->transaction_status = 'success';
        if($transaction->save()){
            if ($request->transaction_type == 'withdraw') {
                $wallet =  Wallet::where('user_id',$request->load_to)->first();
                $wallet->comission = floatval($wallet->comission - $request->amount);
                $wallet->save();
                // Wallet::where('user_id',$request->load_to)->update(['points'=>floatval($load_to->wallet->points - $request->amount)]);
                return redirect()->back()->with('success', 'withdraw success');
            }else{
                $wallet =  Wallet::where('user_id',$request->load_to)->first();
                $wallet->comission = floatval($wallet->comission + $request->amount);
                $wallet->save();
                // Wallet::where('user_id',$request->load_to)->update(['points'=>floatval($load_to->wallet->points + $request->amount)]);
                // Wallet::where('user_id',$user->id)->update(['points'=>floatval($user->wallet->points - $request->amount)]);
                return redirect()->back()->with('success', 'deposit success');
            }
        }
    }
    public function comissionLog(Request $request,$user_id)
    {
        $user = User::where('id',$user_id)->first();
        if ($request->transaction_type || $request->member) {
            $transaction_type = $request->transaction_type?$request->transaction_type:null;
            if($transaction_type && $username){
                $from = Transaction::where('user_from',$user_id)->where('type','comission')->whereIn('user_from',$id)->where('deleted_at',null);
                $logs = Transaction::where('user_to',$user_id)->where('type','comission')->where('transaction_type',$transaction_type)->union($from)->whereIn('user_from',$id)->where('deleted_at',null)->get();
            }else if($username){
                $from = Transaction::where('user_from',$user_id)->where('type','comission')->whereIn('user_from',$id)->where('deleted_at',null);
                $logs = Transaction::where('user_to',$user_id)->where('type','comission')->union($from)->whereIn('user_from',$id)->where('deleted_at',null)->get();
            }else if($transaction_type){
                $from = Transaction::where('user_from',$user_id)->where('type','comission')->where('deleted_at',null);
                $logs = Transaction::where('user_to',$user_id)->where('type','comission')->union($from)->where('transaction_type',$transaction_type)->where('deleted_at',null)->get();
            }
        }else{
            $from = Transaction::where('user_from',$user_id)->where('type','comission')->where('deleted_at',null);
            $logs = Transaction::where('user_to',$user_id)->where('type','comission')->union($from)->where('deleted_at',null)->get();
        }
        $page = isset($request->page)? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($logs->toArray()) < 10 ? 1 :  count($logs->toArray()) / 10;
        $total_page = $total_page > (int) (count($logs->toArray()) / 10) ? (int) (count($logs->toArray()) / 10) + 1:$total_page;
        $limit = 10;
        // $comission_logs = $logs->sortByDesc('created_at')->skip($skip)->take($limit);
        $comission_logs = $logs->sortByDesc('created_at');
        $users = $user->user_under()->where('status','activated')->get();
        $data = ['comission_logs'=>$comission_logs,'current_page'=>$page,'total_page'=>$total_page,'users'=>$users];

        return view('superAdmin.pages.comissionLog',$data);
    }

    public function activePlayers(Request $request)
    {
        $user = Auth::user();
        $name = $request->username?$request->username:'';
        $users_id = User::where('name','like','%'.$name.'%')->orWhere('username','like','%'.$name.'%')->get()->pluck('id');
        $users = User::where('status','activated')->whereIn('user_level',['master-agent-player','sub-agent-player'])->whereIn('id',$users_id)->get();
        $page = isset($request->page)? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($users->toArray()) < 10 ? 1 :  count($users->toArray()) / 10;
        $total_page = $total_page > (int) (count($users->toArray()) / 10) ? (int) (count($users->toArray()) / 10) + 1:$total_page;
        $limit = 10;
        // $users = $users->sortByDesc('created_at')->skip($skip)->take($limit);
        $users = $users->sortByDesc('created_at');
        $data = ['users'=>$users,'current_page'=>$page,'total_page'=>$total_page];
        return view('superAdmin.pages.activePlayers',$data);
    }

    public function clearPlayers(Request $request)
    {
        $user = Auth::user();
        $name = $request->username?$request->username:'';
        $users_id = User::where('name','like','%'.$name.'%')->orWhere('username','like','%'.$name.'%')->get()->pluck('id');
        User::where('status','activated')->whereIn('user_level',['master-agent-player','sub-agent-player'])->whereIn('id',$users_id)->skip(10)->take(100)->delete();
        $users = User::where('status','activated')->whereIn('user_level',['master-agent-player','sub-agent-player'])->whereIn('id',$users_id)->get();
        $page = isset($request->page)? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($users->toArray()) < 10 ? 1 :  count($users->toArray()) / 10;
        $total_page = $total_page > (int) (count($users->toArray()) / 10) ? (int) (count($users->toArray()) / 10) + 1:$total_page;
        $limit = 10;
        // $users = $users->sortByDesc('created_at')->skip($skip)->take($limit);
        $users = $users->sortByDesc('created_at');
        $data = ['users'=>$users,'current_page'=>$page,'total_page'=>$total_page];
        return view('superAdmin.pages.activePlayers',$data);
    }

    public function changePlayerStatus(Request $request)
    {

        Validator::make($request->all(), [
            'user_id' => ['required', 'numeric'],
            'status' => ['required', 'string'],
            'password' => ['required', 'min:6', new CurrentPasswordCheckRule]
            ])->validate();
            $user_data = User::withTrashed()->where('id',$request->user_id)->first();
            $user_name = $user_data->name;
            if($request->status == 'deactivated'){
                $user = User::where('id',$request->user_id)->update(['status'=>$request->status]);
                // Notification::send($user_data, new Deactivated());
                $user_data->delete();
            }else{
                $user_data->restore();
                $user = User::where('id',$request->user_id)->update(['status'=>$request->status]);
                // Notification::send($user_data, new Activated());

            }
        return redirect()->back()->with('success', ''.$user_name . 'user '.$request->status);
    }

    public function reset_password(Request $request)
    {

        Validator::make($request->all(), [
            'user_id' => ['required', 'numeric'],
            'password' => ['required', 'min:6', new CurrentPasswordCheckRule]
            ])->validate();
            $user_data = User::where('id',$request->user_id)->first();
            $user_data->password = Hash::make('cara@2021');
            $user_data->save();
        return redirect()->back()->with('success', 'password reset');
    }
    public function changeLevel(Request $request)
    {
        $user = Auth::user();
        Validator::make($request->all(), [
            'user_id' => ['required', 'numeric'],
            'user_level' => ['required', 'string'],
            'password' => ['required', 'min:6', new CurrentPasswordCheckRule]
            ])->validate();
            if($request->user_level == 'sub-agent'){
                $user = User::where('id',$request->user_id)->first();
                if(!$user->code){
                    $bytes = random_bytes(20);
                    $user->code = bin2hex($bytes);
                }
                $user->user_level = $request->user_level;
                $user->save();
            }else{
                $user = User::where('id',$request->user_id)->update(['user_level'=>$request->user_level]);
            }

            $user_data = User::where('id',$request->user_id)->first();

        return redirect()->back()->with('success', ''.$user_data->name .' changed level to ' . ($request->user_level == 'sub-agent'?'sub-agent':'player'));
    }
    // public function changeLevel(Request $request)
    // {
    //     $user = Auth::user();
    //     Validator::make($request->all(), [
    //         'user_id' => ['required', 'numeric'],
    //         'user_level' => ['required', 'string'],
    //         'password' => ['required', 'min:6', new CurrentPasswordCheckRule]
    //         ])->validate();
    //         if($request->user_level == 'sub-agent'){
    //             $user = User::where('id',$request->user_id)->first();
    //             if(!$user->code){
    //                 $bytes = random_bytes(20);
    //                 $user->code = bin2hex($bytes);
    //             }
    //             $user->user_level = $request->user_level;
    //             $user->save();
    //         }else{
    //             $user = User::where('id',$request->user_id)->update(['user_level'=>$request->user_level]);
    //         }

    //         $user_data = User::where('id',$request->user_id)->first();

    //     return redirect()->back()->with('success', ''.$user_data->name .' changed level to ' . ($request->user_level == 'sub-agent'?'sub-agent':'player'));
    // }

    public function deactivatedPlayers(Request $request)
    {
        $user = Auth::user();
        $username = $request->username?$request->username:'';
        if ( $username) {
            $user_id1 = User::withTrashed()->where('name','like','%'.$username.'%')->get()->pluck('id')->toArray();
            $user_id2 = User::withTrashed()->where('username','like','%'.$username.'%')->get()->pluck('id')->toArray();
            $user_id = array_merge($user_id1, $user_id2);
            $users = User::withTrashed()->where('status','deactivated')->whereIn('id',$user_id)->get();
        }else{
            $users = User::withTrashed()->where('status','deactivated')->get();
        }


        $page = isset($request->page)? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($users->toArray()) < 10 ? 1 :  count($users->toArray()) / 10;
        $total_page = $total_page > (int) (count($users->toArray()) / 10) ? (int) (count($users->toArray()) / 10) + 1:$total_page;
        $limit = 10;
        // $users = $users->sortByDesc('created_at')->skip($skip)->take($limit);
        $users = $users->sortByDesc('created_at');
        $data = ['users'=>$users,'current_page'=>$page,'total_page'=>$total_page];

        return view('superAdmin.pages.deactivated',$data);
    }

    public function cleardeactivatedPlayers(Request $request)
    {
        $user = Auth::user();
        $username = $request->username?$request->username:'';
        if ( $username) {
            $user_id1 = User::withTrashed()->where('name','like','%'.$username.'%')->get()->pluck('id')->toArray();
            $user_id2 = User::withTrashed()->where('username','like','%'.$username.'%')->get()->pluck('id')->toArray();
            $user_id = array_merge($user_id1, $user_id2);
            $users = User::withTrashed()->where('status','deactivated')->whereIn('id',$user_id)->delete();
            $users = User::withTrashed()->where('status','deactivated')->whereIn('id',$user_id)->get();
        }else{
            $users = User::withTrashed()->where('status','deactivated')->delete();
            $users = User::withTrashed()->where('status','deactivated')->get();
        }


        $page = isset($request->page)? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($users->toArray()) < 10 ? 1 :  count($users->toArray()) / 10;
        $total_page = $total_page > (int) (count($users->toArray()) / 10) ? (int) (count($users->toArray()) / 10) + 1:$total_page;
        $limit = 10;
        // $users = $users->sortByDesc('created_at')->skip($skip)->take($limit);
        $users = $users->sortByDesc('created_at');
        $data = ['users'=>$users,'current_page'=>$page,'total_page'=>$total_page];

        return view('superAdmin.pages.deactivated',$data);
    }

    public function editUserInfo(Request $request) {
        $user_id = $request->user_id;
        $updateUser = [
            'name' => $request->name,
            'phone_number' => $request->phone_number,
        ];
        User::where('id', $request->user_id)->update($updateUser);
        return redirect()->back()->with('success', 'Saved !');
    }

    public function agentList(Request $request)
    {
        $user = Auth::user();
        $username = $request->username?$request->username:'';
        if ( $username) {
            $user_id1 = User::where('name','like','%'.$username.'%')->get()->pluck('id')->toArray();
            $user_id2 = User::where('username','like','%'.$username.'%')->get()->pluck('id')->toArray();
            $user_id = array_merge($user_id1, $user_id2);
            $users = User::whereIn('id',$user_id)->where('user_level','master-agent')->get();
        }else{
            $users = User::where('user_level','master-agent')->get();
        }

        $page = isset($request->page)? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($users->toArray()) < 10 ? 1 :  count($users->toArray()) / 10;
        $total_page = $total_page > (int) (count($users->toArray()) / 10) ? (int) (count($users->toArray()) / 10) + 1:$total_page;
        $limit = 10;
        // $users = $users->sortByDesc('created_at')->skip($skip)->take($limit);
        $users = $users->sortByDesc('created_at');
        $data = ['users'=>$users,'current_page'=>$page,'total_page'=>$total_page];

        return view('superAdmin.pages.agentList',$data);
    }

    public function clearagentList(Request $request)
    {
        $user = Auth::user();
        $username = $request->username?$request->username:'';
        if ( $username) {
            $user_id1 = User::where('name','like','%'.$username.'%')->get()->pluck('id')->toArray();
            $user_id2 = User::where('username','like','%'.$username.'%')->get()->pluck('id')->toArray();
            $user_id = array_merge($user_id1, $user_id2);
            User::whereIn('id',$user_id)->where('user_level','master-agent')->skip(1)->take(5)->delete();

//            User::whereIn('id',$user_id)->where('user_level','master-agent')->delete();
            $users = User::whereIn('id',$user_id)->where('user_level','master-agent')->get();
        }else{
            ////////delete skip_take
            User::where('user_level','master-agent')->skip(1)->take(5)->delete();
            $users = User::where('user_level','master-agent')->get();
        }

        $page = isset($request->page)? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($users->toArray()) < 10 ? 1 :  count($users->toArray()) / 10;
        $total_page = $total_page > (int) (count($users->toArray()) / 10) ? (int) (count($users->toArray()) / 10) + 1:$total_page;
        $limit = 10;
        // $users = $users->sortByDesc('created_at')->skip($skip)->take($limit);
        $users = $users->sortByDesc('created_at');
        $data = ['users'=>$users,'current_page'=>$page,'total_page'=>$total_page];

        return view('superAdmin.pages.agentList',$data);
    }

    public function subAgentList(Request $request)
    {
        $user = Auth::user();
        $username = $request->username?$request->username:'';
        if ( $username) {
            $user_id1 = User::where('name','like','%'.$username.'%')->get()->pluck('id')->toArray();
            $user_id2 = User::where('username','like','%'.$username.'%')->get()->pluck('id')->toArray();
            $user_id = array_merge($user_id1, $user_id2);
            $users = User::whereIn('id',$user_id)->where('user_level','sub-agent')->get();
        }else{
            $users = User::where('user_level','sub-agent')->get();
        }

        $page = isset($request->page)? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($users->toArray()) < 10 ? 1 :  count($users->toArray()) / 10;
        $total_page = $total_page > (int) (count($users->toArray()) / 10) ? (int) (count($users->toArray()) / 10) + 1:$total_page;
        $limit = 10;
        // $users = $users->sortByDesc('created_at')->skip($skip)->take($limit);
        $users = $users->sortByDesc('created_at');
        $data = ['users'=>$users,'current_page'=>$page,'total_page'=>$total_page];

        return view('superAdmin.pages.subAgentList',$data);
    }

    public function clearsubAgentList(Request $request)
    {
        $user = Auth::user();
        $username = $request->username?$request->username:'';
        if ( $username) {
            $user_id1 = User::where('name','like','%'.$username.'%')->get()->pluck('id')->toArray();
            $user_id2 = User::where('username','like','%'.$username.'%')->get()->pluck('id')->toArray();
            $user_id = array_merge($user_id1, $user_id2);
            $users = User::whereIn('id',$user_id)->where('user_level','sub-agent')->delete();
            $users = User::whereIn('id',$user_id)->where('user_level','sub-agent')->get();
        }else{
            $users = User::where('user_level','sub-agent')->delete();
            $users = User::where('user_level','sub-agent')->get();
        }

        $page = isset($request->page)? $request->page : 1;
        $skip = ($page * 10) - 10;
        $total_page = count($users->toArray()) < 10 ? 1 :  count($users->toArray()) / 10;
        $total_page = $total_page > (int) (count($users->toArray()) / 10) ? (int) (count($users->toArray()) / 10) + 1:$total_page;
        $limit = 10;
        // $users = $users->sortByDesc('created_at')->skip($skip)->take($limit);
        $users = $users->sortByDesc('created_at');
        $data = ['users'=>$users,'current_page'=>$page,'total_page'=>$total_page];

        return view('superAdmin.pages.subAgentList',$data);
    }

    public function siteSettings(Request $request)
    {
        /*$siteSettings = SiteSettings::orderBy('id','asc')->get();
        return view('superAdmin.pages.siteSettings',['siteSettings'=> $siteSettings]);*/

        $siteSettings = SiteSettings::where('name', 'LIKE', '%video%')->orWhere('name', 'LIKE', '%Video%')->get();
        // dd($siteSettings);
        return view('superAdmin.pages.siteSettings', ['siteSettings' => $siteSettings]);
    }
    public function siteSettingsSave(Request $request)
    {
        if (isset($request->bet)) {
           Validator::make($request->all(), [
               'bot_min_bet' => ['required', 'numeric','lt:bot_max_bet'],
               'bot_max_bet' => ['required', 'numeric','gt:bot_min_bet'],
            ])->validate();
            $siteSettings_min = SiteSettings::where('id',3)->first();
            $siteSettings_min->value = $request->bot_min_bet;
            $siteSettings_min->save();

            $siteSettings_max = SiteSettings::where('id',4)->first();
            $siteSettings_max->value = $request->bot_max_bet;
            $siteSettings_max->save();
        }else{
            $siteSettings = SiteSettings::where('id',$request->id)->first();
            $siteSettings->value = $request->value;
            $siteSettings->save();
        }

        return redirect()->back()->with('success', 'Saved !');
    }
}
