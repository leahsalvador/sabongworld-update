<?php

namespace App\Http\Controllers;

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

class TransactionController extends Controller
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
    public function showTransactionLog(request $request) {
        return view('transactionLog');
    }
}
