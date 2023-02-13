@extends('layouts.header', ['player' => $player])

<style>
    .select-dropdown {
        color: white;
    }
    tr{
        border-bottom: 1px solid #e0e0e0 !important; 
    }
</style>
@section('content')
    <div class="row">
        <div class="col s12 m3">
            <div class="card" style="background-color: #343a40">
                <div class="row">
                    <div class="col s6 m6">
                        <div class="row">
                            <div class="row">
                                <div class="col s12 m12  black-text center">
                                    <h6 class="white-text ">
                                        Betting
                                    </h6>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col s12 m12  black-text center">
                                    <button class="waves-effect waves-light btn-flat white-text small" style=" text-transform: uppercase;">{{$game_rounds_current->status}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col s6 m6">
                        <div class="row">
                            <div class="col s12 m12  black-text center">
                                <h6 class="white-text ">
                                    Round #
                                </h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col s12 m12  black-text center">
                                <button class="waves-effect waves-light btn-flat white-text small" style=" text-transform: uppercase;">{{$game_rounds_current->id}}</button>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 m12  black-text center">
                        <span class="card-title white-text">Past Matches</span>
                    </div>
                    <div class="col s12 m12  black-text center">
    
                        <table class="responsive-table centered highlight white-text">
                            <thead>
                                <tr>
                                    <th>Round</th>
                                    <th>Winner</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($game_rounds as $game_round)
                                <tr>
                                    <td>{{$game_round->id}}</td>
                                    <td class="{{$game_round->winner== 'none' ? 'grey-text':'green-text'}}" style=" text-transform: uppercase;">{{$game_round->winner}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                </div>
                </div>
            </div>
        </div>
        <div class="col s12 m6">
            <div class="card" style="background-color: #343a40">
                <div class="card-content">
                    <span class="card-title white-text center">Cara Cruz</span>
                    <div class="row">
                        <div class="col s2 m3"></div>
                        <div class="col s3 m3" id="coin-1">
                            <div class="side-a"></div>
                            <div class="side-b"></div>
                        </div>
                        <div class="col m2"></div>
                        <div class="col s3 m3" id="coin-2">
                            <div class="side-a"></div>
                            <div class="side-b"></div>
                        </div>
                        <div class="col s2"></div>
                    </div>

                </div>
                <div class="card-action">

                    <div class="row">
                        <div class="amber col s6 m6">
                            <div class="row">
                                <div class="col s12 m12 black-text center amber darken-3">
                                    <h5 class="white-text ">
                                        Heads
                                    </h5>
                                </div>
                                <div class="col s12 m12 black-text center" style="margin-top: 1rem;">
                                    <h6 class="black-text">
                                        {{$game_rounds_current->total_bet_heads}}
                                    </h6>
                                </div>
                                <div class="col s12 m12  black-text center" style="margin-top: 1rem;">
                                    <h5 class="black-text">
                                        {{ $betting_histories_current['heads'] .' = '. ($betting_histories_current['heads'] * 2)}}
                                    </h5>
                                </div>
                                {{-- <div class="center"> --}}
                                <button style="background-color: #343a40"
                                    class="waves-effect waves-light btn white-text col s10 m10 offset-s1 offset-m1"
                                    id="heads">Choose Head</button>
                                {{-- </div> --}}
                            </div>
                        </div>

                        <div class="grey col s6 m6">
                            <div class="row">
                                <div class="col s12 m12  black-text center grey darken-3">
                                    <h5 class="white-text ">
                                        Tails
                                    </h5>
                                </div>
                                <div class="col s12 m12  black-text center" style="margin-top: 1rem;">
                                    <h6 class="black-text">
                                        {{$game_rounds_current->total_bet_tails}}
                                    </h6>
                                </div>
                                <div class="col s12 m12  black-text center" style="margin-top: 1rem;">
                                    <h5 class="black-text">
                                        {{ $betting_histories_current['tails'] .' = '. ($betting_histories_current['tails'] * 2)}}
                                    </h5>
                                </div>
                                {{-- <div class="center" > --}}
                                <button style="background-color: #343a40"
                                    class="waves-effect waves-light btn white-text col s10 m10 offset-s1 offset-m1"
                                    id="tails">Choose Tails</button>
                                {{-- </div> --}}
                            </div>
                        </div>
                        <div class="col s12 m12">
                            <div class="row">
                                <div class="input-field col s6 m6 offset-s6 offset-m6 white-text right" style="all: unset;">
                                    Current Points: {{$player->wallet->points}}
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <form id="betForm"action="{{ url('player/bet') }}" method="post">
                                            @csrf
                                            <input id="amount" type="number" name="amount" class="validate white-text" required placeholder="0" min="1">
                                            <label for="amount">Enter Amount</label>
                                            @if ($errors->has('amount'))
                                            <span class="invalid-feedback red-text" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('amount') }}</strong>
                                            </span>
                                        @endif
                                            <input id="betSide" type="text" name="side" class="validate white-text" hidden>
                                            <input type="text" name="game_rounds_id" class="validate white-text" value="{{$game_rounds_current->id}}" hidden>
                                        </form>
                                    </div>
                                  </div>
                                <button onClick="setPrice(100)" style="background-color: #343a40"
                                    class="btn btn-large waves-effect waves-light white-text">100</button>
                                <button onClick="setPrice(300)" style="background-color: #343a40"
                                    class="btn btn-large waves-effect waves-light white-text">300</button>
                                <button onClick="setPrice(500)" style="background-color: #343a40"
                                    class="btn btn-large waves-effect waves-light white-text">500</button>
                                <button onClick="setPrice(1000)" style="background-color: #343a40"
                                    class="btn btn-large waves-effect waves-light white-text">1000</button>
                                <button onClick="setPrice(2000)" style="background-color: #343a40"
                                    class="btn btn-large waves-effect waves-light white-text">2000</button>
                                <button onClick="setPrice(5000)" style="background-color: #343a40"
                                    class="btn btn-large waves-effect waves-light white-text">5000</button>
                                <button onClick="setPrice(10000)" style="background-color: #343a40"
                                    class="btn btn-large waves-effect waves-light white-text">10000</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="waves-effect waves-light btn" id="flipAll">click me to flip</button>
            </div>
        </div>
        <div class="col s12 m3">
            <div class="card" style="background-color: #343a40">
                <div class="card-content">
                    <span class="card-title white-text">Betting History</span>
                    <table class="responsive-table highlight white-text">
                        <thead>
                            <tr>
                                <th>Side</th>
                                <th>Bet</th>
                                <th>Win</th>
                                <th>Loose</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @if (count($betting_histories) > 0)
                            @foreach ($betting_histories as $betting_history)
                            <tr>
                                <td>{{$betting_history->side}}</td>
                                <td>{{$betting_history->amount}}</td>
                                <td>{{$betting_history->win_amount}}</td>
                                <td>{{$betting_history->loose_amount}}</td>

                                @if ($betting_history->status == 'win')
                                    
                                <td class="green-text" style=" text-transform: uppercase;">{{$betting_history->status}}</td>
                                @elseif($betting_history->status == 'loose')
                                
                                <td class="red-text" style=" text-transform: uppercase;">{{$betting_history->status}}</td>
                                @elseif($betting_history->status == 'cancelled')

                                <td class="grey-text" style=" text-transform: uppercase;">{{$betting_history->status}}</td>
                                @else
                                <td class="orange-text" style=" text-transform: uppercase;">{{$betting_history->status}}</td>
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

<script src="{{asset('js/coin.js')}}"></script>

    @extends('layouts.footer')
@stop
