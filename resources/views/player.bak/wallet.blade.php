@extends('layouts.header')

<style>
    .select-dropdown {
        color: white;
    }
    tr{
        border-bottom: 1px solid #e0e0e0 !important; 
    }
    .channel li {
        background-color: #343a40 !important;
        color: white !important;
        /* border-bottom: 1px solid rgba(0,0,0,0.12) !important; */
    }

</style>
@section('content')

    <div class="row">
        <div class="col s12 m4">
            <div class="card" style="background-color: #343a40">
                <div class="card-content center">
                    <span class="card-title white-text"><i class="material-icons medium">account_balance_wallet</i></span>
                    <div class="row">
                        <div class="col s12 m12 white-text" style="margin-top: 1rem;">
                            <h6>Total Balance</h6>
                        </div>
                        <div class="col s12 m12 white-text">
                            <h5>{{$player->wallet->points}}</h5>
                            @if ($errors->has('amount'))
                            <span class="invalid-feedback red-text" style="display: block;" role="alert">
                                <strong>{{ $errors->first('amount') }}</strong>
                            </span>
                        @endif
                        </div>
                        <div class="col s6 m6">
                            <button style="background-color: #343a40"
                                class="waves-effect waves-light btn white-text col s10 m10 offset-s1 offset-m1 modal-trigger"
                                href="#cashIn">Add
                                Points</button>
                        </div>
                        <div class="col s6 m6">
                            <button style="background-color: #343a40"
                                class="waves-effect waves-light btn white-text col s10 m10 offset-s1 offset-m1 modal-trigger"
                                href="#withdraw">Withdraw</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s12 m4">
            <div class="card" style="background-color: #343a40">
                <div class="card-content">
                    <div class="center">
                        <span class="card-title white-text"><i class="material-icons medium">history</i></span>
                        <div class="row">
                            <div class="col s12 m12 white-text" style="margin-top: 1rem;">
                                <h6>Transaction History</h6>
                            </div>
                        </div>
                        <table class="responsive-table highlight white-text">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Account Type</th>
                                    <th>Account Number</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @if (count($transactions) > 0)
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{$transaction->transaction_type}}</td>
                                    <td>{{$transaction->amount}}</td>
                                    @if ($transaction->transaction_type == 'deposit')
                                    <td>{{$transaction->paymentMethod->account_type}}</td>
                                    <td>{{$transaction->paymentMethod->account_number}}</td>
                                    @else
                                    <td>{{$transaction->account_type}}</td>
                                    <td>{{$transaction->account_number}}</td>
                                    @endif
                                    @if ($transaction->transaction_status === 'pending' )
                                    <td class="orange-text">{{$transaction->transaction_status}}</td>
                                    
                                    @elseif($transaction->transaction_status === 'success')
                                    <td class="green-text">{{$transaction->transaction_status}}</td>
                                    
                                    @elseif($transaction->transaction_status === 'cancelled' )
                                    <td class="green-text">{{$transaction->transaction_status}}</td>
                                        
                                    @endif
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td>none</td>
                                    <td>none</td>
                                    <td>none</td>
                                    <td>none</td>
                                    <td>none</td>
                                </tr>
                                @endif
                               
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
        <div class="col s12 m4">
            <div class="card" style="background-color: #343a40">
                <div class="card-content">
                    <div class="center">
                        <span class="card-title white-text"><i class="material-icons medium">account_balance</i></span>
                        <div class="row">
                            <div class="col s12 m12 white-text" style="margin-top: 1rem;">
                                <h6>Payment Channel</h6>
                            </div>
                        </div>
                    </div>
                    <table class="responsive-table highlight white-text">
                        <thead>
                            <tr>
                                <th>Account Type</th>
                                <th>Account Name</th>
                                <th>Account Number</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($paymentMethods as $paymentMethod)
                                <tr>
                                    <td>{{$paymentMethod->account_type}}</td>
                                    <td>{{$paymentMethod->account_name}}</td>
                                    <td>{{$paymentMethod->account_number}}</td>
                                <tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Structure -->
    <div id="cashIn" class="modal" style="background-color: #343a40">
        <div class="modal-content white-text">
            <h4>Add Points</h4>
            <div class="row">
                <form class="col s12"  id='depositForm' method="POST" action="deposit">
                    @csrf
                    {{-- <div class="row">
                        <div class="input-field col s12 m12">
                            <input id="accountNameDeposit" type="text" placeholder="Account Name" style="pointer-events: none" required class="validate white-text">
                            <label for="first_name">Account Name</label>
                        </div>
                    </div> --}}
                    <div class="row">
                        <div class="input-field col s12 m6">
                            <select id="accountTypeDeposit" name="payment_method_id"  required>
                            {{-- <select id="accountTypeDeposit" name="payment_method_id" onChange="payment('accountTypeDeposit');" required> --}}
                                <option value="" disabled selected>Choose your option</option>
                                @foreach ($paymentMethods as $paymentMethod)                                    
                                <option id="{{$paymentMethod}}"  value="{{$paymentMethod->id}}">{{$paymentMethod->account_type}}</option>
                                @endforeach
                            </select>
                            <label>Account Type</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <input placeholder="0" min='1' required id="amount" name="amount" type="number" required class="validate white-text">
                            <label for="disabled">Amount</label>
                        </div>
                        <input name="agent_id" type="number" id="accountUserIdDeposit" hidden  value="{{$player->agentId()->first()->id}}">
                        {{-- <input name="payment_method_id" type="number" id="accountIdDeposit" hidden  > --}}
                        {{-- <div class="input-field col s12 m6">
                            <input id="accountNumberDeposit" style="pointer-events: none" placeholder="Account Number" name="accountNumber" type="text" required
                                class="validate white-text">
                            <label for="first_name">Account Number</label>
                        </div> --}}
                    </div>
                    <div class="row">
                        <div class="file-field input-field col s12 m12">
                            <div class="btn" style="background-color: #343a40">
                                <span>Slip</span>
                                <input type="file" name="deposit_slip" required>
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path validate white-text" type="text">
                            </div>
                        </div>
                    </div>

                            <button type="submit" class=" right waves-effect waves-green btn white-text"
                            style="background-color: #343a40">Submit</button>
                </div>
            </div>
            {{-- <div class="modal-footer white-text" style="background-color: #343a40">
                <button type="submit" class="waves-effect waves-green btn white-text"
                style="background-color: #343a40">Submit</button>
            </div> --}}
        </form>
    </div>
    <!-- Modal Structure -->
    <div id="withdraw" class="modal" style="background-color: #343a40">
        <div class="modal-content white-text">
            <h4>Withdraw</h4>
            <div class="row">
                <form class="col s12" id='withdrawForm' method="POST" action="withdraw">
                    @csrf
                    <div class="row">
                        <div class="input-field col s12 m6">
                            <input id="accountName"  name="account_name" type="text" placeholder="Account Name" required class="validate white-text ">
                            <label for="first_name">Account Name</label>
                        @if ($errors->has('account_name'))
                            <span class="invalid-feedback red-text" style="display: block;" role="alert">
                                <strong>{{ $errors->first('account_name') }}</strong>
                            </span>
                        @endif
                        </div>
                        <div class="input-field col s12 m6">
                                <input id="Account_type" name="account_type" type="text" placeholder="Account Type" required class="validate white-text">
                                <label for="first_name">Account Type</label>
                                @if ($errors->has('account_type'))
                                <span class="invalid-feedback red-text" style="display: block;" role="alert">
                                    <strong>{{ $errors->first('account_type') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 m6">
                            <input id="accountNumberWithdraw" placeholder="Account Number" name="account_number" type="number" required class="validate white-text">
                            <label for="first_name">Account Number</label>
                            @if ($errors->has('account_number'))
                            <span class="invalid-feedback red-text" style="display: block;" role="alert">
                                <strong>{{ $errors->first('account_number') }}</strong>
                            </span>
                        @endif
                        </div>
                        <div class="input-field col s6">
                            <input placeholder="0" min='100' required id="amount" name="amount" type="number"  class="validate white-text">
                            <label for="disabled">Amount</label>
                            @if ($errors->has('amount'))
                            <span class="invalid-feedback red-text" style="display: block;" role="alert">
                                <strong>{{ $errors->first('amount') }}</strong>
                            </span>
                        @endif
                        </div>
                    </div>
                    <input name="agent_id" type="number" id="accountUserIdWithdraw" hidden  value="{{$player->agentId()->first()->id}}">
                    <button type="submit" class=" right waves-effect waves-green btn white-text"
                    style="background-color: #343a40">Submit</button>

                        {{-- <div class="input-field col s12">
                            <input placeholder="0" min='100' required id="amount" name="amount" type="number" required
                                class="validate white-text">
                            <label for="disabled">Amount</label>
                        </div> --}}
                    </div> 
                </div>
            </div>
            {{-- <div class="modal-footer white-text" style="background-color: #343a40">
                <button type="submit" class="waves-effect waves-green btn white-text"
                style="background-color: #343a40" >Submit</button>
            </div> --}}
        </form>
    </div>
    <script src="{{asset('js/wallet.js')}}"></script>

    @extends('layouts.footer')
@stop
