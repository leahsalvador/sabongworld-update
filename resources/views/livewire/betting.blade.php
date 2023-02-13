<div wire:poll.1000ms>
    <div class="row">
        <div class="col text-white ml-2 h3">
            {{ date('h:i:s A', $now) }}
        </div>
    </div>
    @if (now() < date_time_set(now(), 8, 00) && now() > date_time_set(now(), 6, 00))
        <div class="col text-center "
             style="border-radius: calc(.375rem - 1px) calc(.375rem - 1px) 0 0; background-color: green;">
            <marquee>
                <span class="h3 text-white">&nbsp;</span>
            </marquee>
        </div>
    @else
        @if ($count_down < '00:08' || $count_down > '00:40')
            <div class="col text-center "
                 style="border-radius: calc(.375rem - 1px) calc(.375rem - 1px) 0 0; background-color: green;">
                <marquee>
                    <span class="h3 text-white">&nbsp;</span>
                </marquee>
            </div>
        @else
            @if ($count_down >= '00:31' && $count_down <= '00:40')
                <div class="col text-center "
                     style="border-radius: calc(.375rem - 1px) calc(.375rem - 1px) 0 0; background-color: green;">
                    <marquee>
                        <span
                            class="h3 text-white">{{ strtoupper('') }}</span>
                    </marquee>
                </div>
            @endif
            @if ($count_down >= '00:08' && $count_down <= '00:30')
                <div class="col  text-center"
                     style="border-radius: calc(.375rem - 1px) calc(.375rem - 1px) 0 0; background-color: green;">
                    <marquee>
                        <span class="h3 text-white"><strong>FINAL BETTING</strong></span>
                    </marquee>
                </div>
            @endif
            @if (@$game_rounds_current->status == 'cancelled')
                <div class="col  alert-danger alert-dismissible fade show"
                     style="border-radius: calc(.375rem - 1px) calc(.375rem - 1px) 0 0;">
                    <span class="alert-icon"><i class="fa fa-exclamation-circle"></i></span>
                    <span
                        class="h3 text-white"><strong>{{ strtoupper('') }}</strong></span>
                </div>
            @endif

        @endif
    @endif
    <div class="card-body" style="padding: 0.5rem !important;">
        {{-- @if (@$game_rounds_current->status == 'final-bet' || $count_down <= '00:30') --}}
        <div class="card-body mt--3" style="padding: 0.25rem 0.5rem !important; line-height: 80%;">
            <div class="row">
                <div class="col p-0 text-center">
                    <h5 class="p-2 mb-0 text-center text-white"><strong> {{ __('BETTING') }} </strong></h5>
                </div>
                <div class="col p-0 text-center">
                    <h5 class="p-2 mb-0 text-white"><strong>{{ __('GAME #') }}</strong></h5>
                </div>
            </div>
            <div class="row">
                <div class="col p-2 text-center mr-1">

                    <script>

                        var isRedirectState = 0;

                       /* $.get("{{ url('game-opened') }}", function (data, status) {
                            isRedirectState = data;

                        });*/

                        function redirectCurrentPage() {
                            window.location.href = "{{ url('player') }}";
                        }
                    </script>


                    {{--@if (now() < date_time_set(now(), 8, 00) && now() > date_time_set(now(), 6, 00))--}}
                    {{--<button type="button" class="btn btn-info text-dark btn-block border-light"--}}
                    {{--style="pointer-events: none; ">{{ __('UPCOMING') }}</button>--}}
                    {{--@else--}}

                    @if (@$game_rounds_current->status == 'open')
                        <button type="button" style="background-color: green; pointer-events: none;"
                                class="btn text-white border-light">{{ __('OPEN') }}</button>

                    @elseif( @$game_rounds_current->status == 'final-bet')
                        <button type=" button" class="btn btn-warning border-light"
                                style="pointer-events: none; ">{{ __('FINAL CALL') }} </button>

                    @elseif( @$game_rounds_current->status == 'upcoming')
                        <button type="button" class="btn btn-outline-info border-light"
                                style="pointer-events: none; ">{{ __('UPCOMING') }}</button>

                        <script>

                            if (isRedirectState == 1) {

                                redirectCurrentPage();
                            }
                        </script>

                    @elseif( @$game_rounds_current->status == 'cancelled')
                        <button type="button" style="background-color: #ced4da; pointer-events: none;"
                                class="btn border-light">{{ __('CANCELLED') }}</button>

                    @elseif( @$game_rounds_current->status == 'draw')
                        <button type="button" style="background-color: green; pointer-events: none; color: white;"
                                class="btn border-light">{{ __('DRAW') }}</button>

                    @elseif( @$game_rounds_current->status == 'closed' || @$game_rounds_current->status == 'done' )
                        <button type="button" style="background-color: red; pointer-events: none;"
                                class="btn text-white border-light">{{ __('CLOSED') }}</button>
                        <script>

                            if (isRedirectState == 1 || isRedirectState == 2) {
                                redirectCurrentPage();
                            }
                        </script>
                    @elseif( @$game_rounds_current->status == 'undo' )
                        <button type="button" style="background-color: red; pointer-events: none;"
                                class="btn text-white border-light">{{ __('UNDO') }}</button>

                    @else

                        <input type="hidden" id="hide-game-state" value="5">

                    @endif
                    {{--@endif--}}
                </div>
                <div class="col p-0 text-center ml-1">
                    <h5 class="p-2 mb-0 text-white"><strong> {{ @$game_rounds_current->round ?? 0 }}</strong></h5>
                </div>
            </div>
            <div class="row">
            </div>
            <div class="row">
                <div class="col p-0 text-center dark-right-border dark-bg">
                    <h3 class="text-yellow" style="margin-bottom: auto;font-size: 1.2rem !important;">
                        {{ number_format($betting_histories_current["countHeads"], 2) }} </h3>
                    <h3 class="text-white" style="margin-bottom: auto">
                        PAYOUT = {{ number_format($betting_histories_current["payout_heads"], 2) }}
                    </h3>
                </div>
                <div class="col p-0 text-center dark-bg">
                    <h3 class="text-yellow" style="margin-bottom: auto; font-size: 1.2rem !important;">
                        {{ number_format($betting_histories_current["countTails"], 2) }} </h3>
                    <h3 class="text-white" style="margin-bottom: auto">
                        PAYOUT = {{ number_format($betting_histories_current["payout_tails"], 2) }}
                    </h3>
                </div>
            </div>
            <div class="row">
                <div class="col p-1 text-center pt-0 dark-right-border dark-bg">

                    @if (!empty(@$game_rounds_current->winner) && in_array(@$game_rounds_current->winner, ['heads', 'tails']))
                        <h3 style="color: #24d9b0" id="current_head" style="margin-bottom: auto">0</h3>
                    @else
                        @if (@$game_rounds_current->head_payout > 0)
                            @if (number_format(@$betting_histories_current['heads']) > 0)
                                <h3 style="color: #24d9b0" id="current_head" style="margin-bottom: auto">
                                    {{ number_format(@$betting_histories_current['heads'], 2, '.', ',') ?? 0 }} =
                                    {{ number_format(@$betting_histories_current['heads'] / 100 * @$betting_histories_current['payout_heads'], 2, '.', ',') }}
                                </h3>

                            @else
                                <h3 style="color: #24d9b0" id="current_head" style="margin-bottom: auto">
                                    {{ number_format(@$betting_histories_current['heads'], 2) ?? 0 }} </h3>
                            @endif

                        @else
                            <h3 style="color: #24d9b0" id="current_head" style="margin-bottom: auto">
                                {{ number_format(@$betting_histories_current['heads'], 2) ?? 0 }} </h3>
                        @endif
                    @endif

                    @if (now() < date_time_set(now(), 8, 00) && now() > date_time_set(now(), 6, 00))
                        <button id="heads" type="button" style="background-color: red"
                                {{ @$game_rounds_current->status == 'closed' || @$game_rounds_current->status == 'done' || @$game_rounds_current->status == 'cancelled' ? 'disabled' : '' }}
                                class="btn btn-success text-white col-sm-12 btn-heads @if( @$game_rounds_current->status == 'closed' || @$game_rounds_current->status =='done') disabled @endif"
                                @if( @$game_rounds_current->status == 'closed' || @$game_rounds_current->status =='done') disabled @endif>
                            <i class="fas fa-plus-circle"></i>
                            MERON
                        </button>
                    @else
                        <button id="heads" type="button" style="background-color: red"
                                {{ @$game_rounds_current->status == 'closed' || @$game_rounds_current->status == 'done' || @$game_rounds_current->status == 'cancelled' ? 'disabled' : '' }}
                                class="btn btn-success text-white col-sm-12  btn-heads @if( @$game_rounds_current->status == 'closed' || @$game_rounds_current->status =='done') disabled @endif"
                                @if( @$game_rounds_current->status == 'closed' || @$game_rounds_current->status =='done') disabled @endif>
                            <i class="fas fa-plus-circle"></i>
                            MERON
                        </button>
                    @endif
                </div>
                <div class="col p-1 text-center pt-0 dark-right-border dark-bg">
                    @if (!empty(@$game_rounds_current->winner) && in_array(@$game_rounds_current->winner, ['heads', 'tails']))
                        <h3 style="color: #24d9b0" id="current_tails" style="margin-bottom: auto">0</h3>
                    @else
                        @if (@$game_rounds_current->head_payout > 0)
                            @if (number_format(@$betting_histories_current['tails']) > 0)
                                <h3 style="color: #24d9b0" id="current_head" style="margin-bottom: auto">
                                    {{ number_format(@$betting_histories_current['tails'], 2, '.', ',') ?? 0 }} =
                                    {{ number_format(@$betting_histories_current['tails'] / 100 * @$betting_histories_current['payout_tails'], 2, '.', ',') }}
                                </h3>
                            @else
                                <h3 style="color: #24d9b0" id="current_tails" style="margin-bottom: auto">
                                    {{ number_format(@$betting_histories_current['tails'],2) ?? 0 }}</h3>
                            @endif

                        @else
                            <h3 style="color: #24d9b0" id="current_tails" style="margin-bottom: auto">
                                {{ number_format(@$betting_histories_current['tails'], 2) ?? 0 }}</h3>
                        @endif
                    @endif
                    @if (now() < date_time_set(now(), 8, 00) && now() > date_time_set(now(), 6, 00))
                        <button id="tails" type="button" style="background-color: blue"
                                class="btn btn-success text-white col-sm-12  btn-tails @if( @$game_rounds_current->status == 'closed' || @$game_rounds_current->status =='done') disabled @endif"
                                @if( @$game_rounds_current->status == 'closed' || @$game_rounds_current->status =='done') disabled @endif>
                            <i class="fas fa-plus-circle"></i>
                            WALA
                        </button>
                    @else
                        <button id="tails" type="button" style="background-color: blue"
                                {{ @$game_rounds_current->status == 'closed' || @$game_rounds_current->status == 'done' || @$game_rounds_current->status == 'cancelled' ? 'disabled' : '' }}
                                class="btn btn-success text-white col-sm-12  btn-tails @if( @$game_rounds_current->status == 'closed' || @$game_rounds_current->status =='done') disabled @endif"
                                @if( @$game_rounds_current->status == 'closed' || @$game_rounds_current->status =='done') disabled @endif>
                            <i class="fas fa-plus-circle"></i>
                            WALA
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <ul class="list-group list-group-flush ">
            <li class="list-group-item bg-default text-white ">
                <div class="row">
                    <form id="betForm" action="/player/bet" method="post">
                        @csrf
                        <div class="form-group">
                            <input type="number" id="current_wallet" value="{{ @$wallet->points }}" hidden>
                            <label for="exampleFormControlInput1" class="float-right" id="wallet_current">Current
                                Points :
                                <strong style="color: #ffdc11">
                                    {{ number_format(@$wallet->points, 2, '.', ',') }}</strong></label>
                            <div class="input-group mb-2">
                                <input class="form-control text-white bg-default bet-amount" type="number" step="100"
                                       name="amount" placeholder="Bet Amount" aria-label="Bet Amount"
                                       aria-describedby="button-addon2">
                                <div class="input-group-append">
                                    <button class="btn text-white" style="background-color: green" type="reset"
                                            id="button-addon2" >CLEAR
                                    </button>
                                </div>
                                <input id="betSide" type="text" name="side" class="validate white-text" hidden>
                                <input type="number" id="currentRound" name="game_rounds_id" class="validate white-text"
                                       hidden value="{{ @$game_rounds_current->id }}">
                            </div>
                        </div>
                    </form>
                    <form class="form-inline">
                        @if($minimum_bet < 100)
                            <button type="button" data-amount="{{$minimum_bet}}" class="btn text-white btn-set-price border-light btn-sm"
                                    style="background-color: green">{{$minimum_bet}}
                            </button>
                        @endif
                        <button type="button" data-amount="100" class="btn text-white btn-set-price border-light btn-sm"
                                style="background-color: green">100
                        </button>
                        <button type="button" data-amount="200" class="btn text-white btn-set-price border-light btn-sm"
                                style="background-color: green">200
                        </button>
                        <button type="button" data-amount="500" class="btn text-white btn-set-price border-light btn-sm"
                                style="background-color: green">500
                        </button>
                        <button type="button" data-amount="1000"
                                class="btn text-white btn-set-price border-light btn-sm"
                                style="background-color: green">1000
                        </button>
                        <button type="button" data-amount="5000"
                                class="btn text-white btn-set-price border-light btn-sm"
                                style="background-color: green">5000
                        </button>
                        <button type="button" data-amount="10000"
                                class="btn text-white btn-set-price border-light btn-sm"
                                style="background-color: green">10000
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </div>
</div>

