@extends('layouts.appPlayer')

@section('content')
    @livewireStyles()
    <style>
        .floating_play {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 1%;
            right: 1%;
            color: #FFF;
            background-color: #1db954;
            border-radius: 50px;
            text-align: center;
            z-index: 999;
            cursor: pointer;
            border-color: black !important;
            border-style: solid;
            border-width: 2px;
        }

        .my-float {
            margin-top: 20px;
            margin-left: 3px;
        }

        .grid-container {
            display: grid;
            grid-template-rows: repeat(8, 50px);
            grid-template-columns: repeat(1300, 50px);
            grid-auto-flow: column;
            align-item: 'center';
            overflow-y: hidden;
            /*border-radius: 50% !important;*/
        }

        .grid-container div {
            border: solid 1px black;
            width: 50px;
            height: 50px;
        }

        .blink {
            animation: blink 1s linear infinite;
        }

        @keyframes blink {
            0% {
                opacity: 0;
            }

            10% {
                opacity: .2;
            }

            20% {
                opacity: .5;
            }

            30% {
                opacity: .7;
            }

            40% {
                opacity: .9;
            }

            50% {
                opacity: 1;
            }

            60% {
                opacity: .9;
            }

            70% {
                opacity: .7
            }

            80% {
                opacity: .5;
            }

            90% {
                opacity: .2;
            }

            100% {
                opacity: 0;
            }
        }

        #style-1::-webkit-scrollbar-thumb {
            border-radius: 10px;
            -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, .3);
            background-color: #555;
        }

        iframe {
            height: 60vh;
        }

        @media (max-width: 600px) {
            iframe {
                height: 30vh;
            }
        }
    </style>
    @livewireScripts()
    @include('layouts.playerNavbars.headers.cards')
    <input type="hidden" id="game-state" value="0">

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-md-8 mt-3">
                <div class="card bg-default border-light">
                    {{--                    @livewire('flip-coin')--}}
                    <?php
                    $first = '';
                    $current_id = '';
                    $current_id_backup = '';

                    if (!empty($_GET['ri']) && is_numeric($_GET['ri'])) {
                        $current_id = $_GET['ri'];
                    } else {
                        $first = 'active';
                    }

                    if (!$current_id)
                        $current_id = $current;

                    $temp = $current_id;
                    $result = DB::table('game_rounds')->where('id', $current_id)->first();
                    if ($result != null)
                        $winner = $result->winner;
                    else
                        $winner = null;
                    ?>
                    <div class="card-header bg-default border-light"
                         style="padding: 0.25rem 0.5rem !important; line-height: 80%;">
                        <div class="d-flex justify-content-center mb4">
                            @if($winner != null && $winner != "none")
                                @if($winner == "draw")
                                    <p class="blink col  text-white text-center"
                                       style="background-color: #1aff4c; font-size: 30px; font-weight: bold; border-radius: 1rem 1rem 1rem 1rem;">
                                        DRAW</p>
                                @endif
                                @if($winner == "heads")
                                    <p class="blink col  text-white text-center"
                                       style="background-color: red; font-size: 30px; font-weight: bold; border-radius: 1rem 1rem 1rem 1rem;">
                                        WINNER WALA</p>
                                @endif
                                @if($winner == "tails")
                                    <p class="blink col text-white text-center"
                                       style="background-color: blue; font-size: 30px; font-weight: bold; border-radius: 1rem 1rem 1rem 1rem;">
                                        WINNER MERON</p>
                                @endif
                            @elseif($winner == 'none')
                                <p class="blink col  text-white text-center"
                                   style="background-color: #0f1134; font-size: 30px; font-weight: bold; border-radius: 1rem 1rem 1rem 1rem;">
                                    No Result</p>
                            @endif
                        </div>
                    </div>
                    @if($player->wallet->points >= config('settings.video_block'))
                        @if($video_setting)
                            <iframe src="{{ $video_setting->value }}" frameborder="0" allowfullscreen="true"
                                    webkitallowfullscreen="true" mozallowfullscreen="true"></iframe>
                        @endif
                    @endif
                </div>
            </div>

            <div class="col-sm-12 col-md-4 mt-3">
                <div class="card bg-default border-light">
                    <div>
                        <?php
                        $first = '';
                        $current_id = '';
                        $current_id_backup = '';
                        if (!empty($_GET['ri']) && is_numeric($_GET['ri'])) {
                            $current_id = $_GET['ri'];
                        } else {
                            $first = 'active';
                        }
                        $first_bool = false;
                        $matched = false;
                        ?>
                        @foreach($game_rounds as $gr)
                                <?php
                                if (!empty($current_id) && $current_id == $gr->id) {
                                    $matched = true;
                                }
                                ?>
                        @endforeach

                        @foreach($game_rounds as $gr)
                                <?php
                                $btn_color = '-' . $gr->Game_color;
                                if (!empty($current_id) && $current_id == $gr->id) {
                                    $first = 'active';
                                }
                                if (!$first_bool) {
                                    $current_id_backup = $gr->id;
                                    if (!$matched) {
                                        $first = 'active';
                                    }
                                }
                                ?>
                                <?php
                                $first_bool = true;
                                $first = ''; ?>
                        @endforeach
                    </div>
                    <?php
                    $first = 'show active';
                    if (empty($current_id) || !$matched) {
                        $current_id = $current_id_backup;
                    }
                    ?>
                    <div class="card shadow bg-default border-light">
                        <div class="card-body bg-default border-light">
                            <div class="tab-content bg-default border-light" id="myTabContent">
                                <?php
                                $first = 'show active';
                                if (empty($current_id) || !$matched) {
                                    $current_id = $current_id_backup;
                                }
                                ?>
                                <div class="tab-pane fade {{__($first)}} bg-default border-light"
                                     id="tabs-icons-text-{{__($temp)}}" role="tabpanel"
                                     aria-labelledby="tabs-icons-text-{{__($temp)}}-tab">
                                    @livewire('betting', ['game_id' => $current_id])
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item bg-default text-white">
                            @livewire('round-history')
                        </div>
                        <div class="list-group-item bg-default text-white">

                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.footers.auth')
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.js"></script>
    <script>
        function setPrice(price) {
            //document.querySelector('#amount').value = price;
            document.querySelector('.bet-amount').value = price;
        }

        let called = 0;
        let resultAudio = 0;

        async function test(params) {
            called++;
            if (called == 1) {
                /*var coin = new Audio("{{env('APP_URL')}}image/coinss.m4a");
                        coin.play();*/
                if (params.winner == 'tails') {
                    $("#coin-1").addClass("tails");
                    $("#coin-2").addClass("tails");
                    await new Promise(resolve => setTimeout(resolve, 5000));
                    /*let tails = new Audio("{{env('APP_URL')}}image/tails.mp3");
                            tails.play();*/
                    console.log('5sec tails')
                    $('#tails-alert').show();
                    await new Promise(resolve => setTimeout(resolve, 3000));
                    console.log('10sec tails')
                    $('#tails-alert').hide();
                    $("#coin-1").removeClass();
                    $("#coin-2").removeClass();
                    await new Promise(resolve => setTimeout(resolve, 2000));
                    called = 0;
                } else if (params.winner == 'heads') {
                    $("#coin-1").addClass("heads");
                    $("#coin-2").addClass("heads");
                    await new Promise(resolve => setTimeout(resolve, 5000));
                    /*var heads = new Audio("{{env('APP_URL')}}image/heads.mp3");
                            heads.play();*/
                    console.log('5sec heads')
                    $('#heads-alert').show();
                    await new Promise(resolve => setTimeout(resolve, 3000));
                    console.log('10sec heads')
                    $('#heads-alert').hide();
                    $("#coin-1").removeClass();
                    $("#coin-2").removeClass();
                    await new Promise(resolve => setTimeout(resolve, 2000));
                    called = 0;
                } else if (params.winner == 'draw') {
                    let coin1 = params.coin1 == 1 ? 'heads' : 'tails';
                    let coin2 = params.coin2 == 1 ? 'heads' : 'tails';
                    $("#coin-1").addClass(coin1);
                    $("#coin-2").addClass(coin2);
                    await new Promise(resolve => setTimeout(resolve, 5000));
                    /*var draw = new Audio("{{env('APP_URL')}}image/draw.mp3");
                            draw.play();*/
                    $('#draw-alert').show();
                    console.log('5sec heads')
                    await new Promise(resolve => setTimeout(resolve, 2000));
                    console.log('10sec heads')
                    $('#draw-alert').hide();
                    called = 0;
                    $("#coin-1").removeClass();
                    $("#coin-2").removeClass();
                }

            }
        }

        function drawAudio(params) {
            resultAudio++;
            if (resultAudio == 1) {
                var draw = new Audio("{{env('APP_URL')}}image/draw.mp3");
                draw.loop = false;
                draw.play();
                $('#draw-alert').show();
                setTimeout(() => {
                    $('#draw-alert').hide();
                    resultAudio = 0;
                    draw.pause();
                }, 6000);
            }
        }

        function tailsAudio(params) {
            resultAudio++;
            if (resultAudio == 1) {
                var tails = new Audio("{{env('APP_URL')}}image/tails.mp3");
                tails.play();
                $('#tails-alert').show();
                setTimeout(() => {
                    $('#tails-alert').hide();
                    resultAudio = 0;
                    tails.pause();
                }, 6000);
            }
        }

        function headsAudio(params) {
            resultAudio++;
            if (resultAudio == 1) {
                var heads = new Audio("{{env('APP_URL')}}image/heads.mp3");
                heads.play();
                $('#heads-alert').show();
                setTimeout(() => {
                    $('#heads-alert').hide();
                    resultAudio = 0;
                    heads.pause();
                }, 6000);
            }
        }


        var audio = new Audio("{{env('APP_URL')}}image/bgm.mp3");
        if (typeof audio.loop == 'boolean') {
            audio.loop = true;
        } else {
            audio.addEventListener('ended', function () {
                this.currentTime = 0;
                this.play();
            }, false);
        }
        $('#play-pause-button').on("click", function () {
            if ($("#icon-play").hasClass('fa-play')) {
                $("#icon-play").removeClass('fa-play');
                $("#icon-play").addClass('fa-pause');
                audio.play();
            } else {
                $("#icon-play").removeClass('fa-pause');
                $("#icon-play").addClass('fa-play');
                audio.pause();
            }
        });

        audio.onended = function () {
            $("#icon-play").removeClass('fa-pause');
            $("#icon-play").addClass('fa-play');
        };

        var audio = new Audio("{{env('APP_URL')}}image/bgm.mp3");
        if (typeof audio.loop == 'boolean') {
            audio.loop = true;
        } else {
            audio.addEventListener('ended', function () {
                this.currentTime = 0;
                this.play();
            }, false);
        }
        $('#play-pause-button').on("click", function () {
            if ($("#icon-play").hasClass('fa-play')) {
                $("#icon-play").removeClass('fa-play');
                $("#icon-play").addClass('fa-pause');
                audio.play();
            } else {
                $("#icon-play").removeClass('fa-pause');
                $("#icon-play").addClass('fa-play');
                audio.pause();
            }
        });

        audio.onended = function () {
            $("#icon-play").removeClass('fa-pause');
            $("#icon-play").addClass('fa-play');
        };

        function setPrice(price) {
            document.querySelector('#amount').value = price;
        }

        $(document).ready(function () {
            $(document).on('click', '.btn-set-price', function () {
                const tmp_amount = $(this).attr('data-amount');
                $('.bet-amount').val(tmp_amount);
            });
            $(".btn-heads").click((e) => {
                $($(".btn-heads")[0]).parents().find("div.card").children().find("#amount")
                $($(".btn-heads")[0]).parents().find("div.card").children().find("#amount")
                let walletElem = $($(e.target).parents()[3]).children().find("#current_wallet")[0] || undefined;
                let points = $('.bet-amount').val() || 0;
                let balance = walletElem.value || 0;

                if (points == '') {
                    Swal.fire(
                        'No Bet Amount!',
                        'Please set an amount!',
                    )
                    return;
                }
                if (points > parseFloat(balance)) {
                    Swal.fire(
                        'Insufficient Balance!',
                        balance,
                    )
                    return;
                }
                if (points < {{$minimum_bet}}) {
                    Swal.fire(
                        'Minimum Bet {{$minimum_bet}}!',
                        '',
                    )
                    return;
                }
                points.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                Swal.fire({
                    title: 'Are you sure?',
                    text: `You want to bet ${points} points to Meron?`,
                    showCancelButton: true,
                    confirmButtonColor: '#343a40',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, place bet!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let betSide = $($(e.target).parents()[3]).children().find("#betSide")[0] || undefined
                        if (betSide) betSide.value = 'heads';
                        let myForm = $($(e.target).parents()[3]).children().find("#betForm")[0] || undefined
                        //myForm.submit();
                        Swal.fire({
                            title: 'Please wait your request is processing',
                            html: '<p></p>',
                            allowOutsideClick: false
                        });
                        Swal.showLoading();
                        $.ajax({
                            type: "POST",
                            url: "{{env('APP_URL')}}player/bet",
                            data: $(myForm).serialize(),
                            success: betSuccess,
                            fail: betFailed,
                        });
                    }
                })
            });
            $(".btn-tails").click((e) => {
                $($(e.target).parents()[3]).children().find("#amount")[0] || undefined
                let walletElem = $($(e.target).parents()[3]).children().find("#current_wallet")[0] || undefined
                let points = $('.bet-amount').val();
                let balance = walletElem.value;
                if (points == '') {
                    Swal.fire(
                        'Bet Amount not set!',
                        'Please set an amount!',
                    )
                    return;
                }
                if (points > parseFloat(balance)) {
                    Swal.fire(
                        'Insufficient Balance!',
                        balance,
                    )
                    return;
                }
                if (points < {{$minimum_bet}}) {
                    Swal.fire(
                        'Minimum Bet {{$minimum_bet}}!',
                        '',
                    )
                    return;
                }
                points.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                Swal.fire({
                    title: 'Are you sure?',
                    text: `You want to bet ${points} points to Wala?`,
                    showCancelButton: true,
                    confirmButtonColor: '#343a40',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, place bet!'
                }).then((result) => {
                    console.log(result)
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Please wait your request is processing',
                            html: '<p></p>',
                            allowOutsideClick: false
                        });
                        Swal.showLoading();
                        let betSide = $($(e.target).parents()[3]).children().find("#betSide")[0] || undefined
                        if (betSide) betSide.value = 'tails';
                        let myForm = $($(e.target).parents()[3]).children().find("#betForm")[0] || undefined
                        //myForm.submit();
                        $.ajax({
                            type: "POST",
                            url: "{{env('APP_URL')}}player/bet",
                            data: $(myForm).serialize(),
                            success: betSuccess,
                            fail: betFailed,
                        });
                    }
                })
            });
        });

        function betSuccess(response) {
            Swal.close();
            $('.bet-amount').val('');
            console.log(response);
        }

        function betFailed(jqXHR, textStatus, errorThrown) {
            Swal.close();
            console.log(jqXHR, textStatus, errorThrown);
            Swal.fire({
                title: textStatus,
                html: '<p>' + errorThrown + '</p>',
                allowOutsideClick: false
            });
        }

        //setInterval(newGameState, 3000);

        function newGameState() {
            $.get("{{ url('game-opened') }}", function (data, status) {
                if (data == 1) {
                    $("#game-state").val(1);
                } else {
                    $("#game-state").val(0);
                }
            });
        }

        function declareWinner() {
            $.get("{{ url('player/declare/winner') }}", function (data, status) {
                if (!$.isEmptyObject(data)) {
                    let winner = data.winner;
                    if (data.winner != "draw") {
                        winner = data.winner + " Win";
                    }
                    Swal.fire({
                        html: '<div class="text-center text-dark"><p class="h3">Result: ' + data.winner.toUpperCase() + ' <br> Amount: ' + data.amount + '</p></div>',
                        showCloseButton: true,
                        showCancelButton: false,
                        showConfirmButton: false,
                        toast: false,
                        width: '20em',
                        background: '#fff',
                        position: 'center',
                        timer: 5000,
                    });
                }
            });
        }

        setInterval(declareWinner, 1500);
    </script>
@endpush
