<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'Home' }}</title>

    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="{{ asset('css/coin.css') }}" rel="stylesheet">
    <link href="//cdn.jsdelivr.net/npm/@sweetalert2/theme-wordpress-admin@4/wordpress-admin.css" rel="stylesheet">


    <!-- Compiled and minified JavaScript -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        .sidenav {
            position: fixed;
            width: unset;
            left: 0;
            top: 0;
            margin: 0;
            -webkit-transform: translateX(-100%);
            transform: translateX(-100%);
            height: 100%;
            height: calc(100% + 60px);
            height: -moz-calc(100%);
            padding-bottom: 60px;
            background-color: #fff;
            z-index: 999;
            overflow-y: auto;
            will-change: transform;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            -webkit-transform: translateX(-105%);
            transform: translateX(-105%);
        }

    </style>
</head>

<body style="background-color: #343a40">

    <div class="navbar-fixed">
        <nav style="background-color: #13233C">
            <div class="nav-wrapper ">
                <a href="#!" class="brand-logo center" style="margin-top: 5px;"><img
                        src="//logo.clearbit.com/clearbit.com?size=50"></img></a>
                <a href="#" data-target="mobile-demo" class="sidenav-trigger white-text"><i
                        class="material-icons">menu</i></a>
                <ul class="right hide-on-med-and-down">
                    <li><a href="/player" class="white-text"><i class="material-icons left">account_circle</i>{{$player->name}}</a>
                    </li>
                    <li><a href="/player" class="white-text"><i class="material-icons left">home</i>Home</a>
                    </li>
                    <li><a href="/player/wallet" class="white-text"><i class="material-icons left">account_balance_wallet</i>Wallet</a>
                    </li>
                    <li><a href="{{ route('logout') }}"  class="white-text"><i class="material-icons left">exit_to_app</i>Log Out</a></li>
                </ul>
            </div>
        </nav>
    </div>

    <ul class="sidenav" id="mobile-demo">
        <li><a href="/player" class="black-text "><i class="material-icons right">account_circle</i>{{$player->name}} &nbsp;</a>
        </li>
        <li><a href="/player" class="black-text "><i class="material-icons right">home</i>Home &nbsp;</a>
        </li>
        <li><a href="/player/wallet" class="black-text "><i class="material-icons right">account_balance_wallet</i>Wallet &nbsp;</a>
        </li>
        <li><a href="{{ route('logout') }}"  class="black-text "><i class="material-icons right">exit_to_app</i>Log Out &nbsp;</a></li>
    </ul>

    {{-- <!-- Modal Trigger -->
    <a class="waves-effect waves-light btn modal-trigger" href="#modal1">Where to
        deposit</a> --}}

    <!-- Modal Structure -->
    <div id="modal1" class="modal">
        <div class="modal-content">
            <h5>You can deposit your bet through these means</h5>
            <div class="file-field input-field col s12 m12">
                <div class="btn">
                    <span>Deposit Slip</span>
                    <input type="file">
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate white-text" type="text">
                </div>
            </div>
            <ul class="collection">
                <li class="collection-item avatar">
                    <img src="//logo.clearbit.com/bpi.com.ph" alt="" class="circle">
                    <span class="title">BPI</span>
                    <p>Account Name: Gemuel Joy Alcantara <br>
                        Account Number: 832674982327
                    </p>
                    <a href="#!" class="secondary-content"><i class="material-icons">grade</i></a>
                </li>
                <li class="collection-item avatar">
                    <img src="//logo.clearbit.com/gcash.com" alt="" class="circle">
                    <span class="title">GCash</span>
                    <p>Account Name: Gemuel Joy Alcantara <br>
                        Account Number: 09212121212
                    </p>
                    <a href="#!" class="secondary-content"><i class="material-icons">grade</i></a>
                </li>
                <li class="collection-item avatar">
                    <img src="//logo.clearbit.com/bdo.com" alt="" class="circle">
                    <span class="title">BDO</span>
                    <p>Account Name: Gemuel Joy Alcantara <br>
                        Account Number: 832674982327
                    </p>
                    <a href="#!" class="secondary-content"><i class="material-icons">grade</i></a>
                </li>
                <li class="collection-item avatar">
                    <img src="//logo.clearbit.com/paymaya.com" alt="" class="circle">
                    <span class="title">Paymaya</span>
                    <p>Account Name: Gemuel Joy Alcantara <br>
                        Account Number: 0921212121
                    </p>
                    <a href="#!" class="secondary-content"><i class="material-icons">grade</i></a>
                </li>
            </ul>

        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Agree</a>
        </div>
    </div>

    @yield('content')
