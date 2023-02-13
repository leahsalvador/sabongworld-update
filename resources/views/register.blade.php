{{-- <!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'Online Cara-Cruz' }}</title>

    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Compiled and minified JavaScript -->
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        .row .col.s12 {
            width: 100%;
            margin-left: auto;
            margin-top: 0rem;
            left: auto;
            right: auto;
        }

    </style>
</head> --}}
@extends('layouts.app', ['class' => 'bg-default'])

@section('content')
    @include('layouts.headers.guest')
<body style="background-color: #343a40">
    <div class="container">
        <div class="row section">
            <div class="col s1 m3"></div>
            <div class="col s10 m6">
                <div class="card" style="background-color: #343a40">
                    <div class="card-content white-text">
                        <span class="card-title center"><a href="#" class="brand-logo center"><img
                                    src="//logo.clearbit.com/clearbit.com?size=50"></a></span>
                        <div class="row">
                            <form role="form" method="POST" action="{{ route('register') }}">
                                @csrf
    
                                <div class="input-field{{ $errors->has('name') ? ' has-danger' : '' }}">
                                        <input class="form-control white-text{{ $errors->has('name') ? ' is-invalid' : '' }}" placeholder="{{ __('Name') }}" type="text" name="name" value="{{ old('name') }}" required autofocus>
                                    @if ($errors->has('name'))
                                        <span class="invalid-feedback red-text" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="input-field{{ $errors->has('phone_number') ? ' has-danger' : '' }}">
                                        <input class="form-control white-text{{ $errors->has('phone_number') ? ' is-invalid' : '' }}" placeholder="{{ __('63') }}" type="number" name="phone_number" value="{{ old('phone_number') ? old('phone_number'): '63' }}" required autofocus>
                                    @if ($errors->has('phone_number'))
                                        <span class="invalid-feedback red-text" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('phone_number') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="input-field{{ $errors->has('username') ? ' has-danger' : '' }}">
                                        <input class="form-control white-text{{ $errors->has('username') ? ' is-invalid' : '' }}" placeholder="{{ __('Username') }}" type="text" name="username" value="{{ old('username') }}" required autofocus>
                                    @if ($errors->has('username'))
                                        <span class="invalid-feedback red-text" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('username') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="input-field{{ $errors->has('email') ? ' has-danger' : '' }}">
                                        <input class="form-control white-text{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('Email') }}" type="email" name="email" value="{{ old('email') }}" required>
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback red-text" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="input-field{{ $errors->has('password') ? ' has-danger' : '' }}">
                                        <input class="form-control white-text{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ __('Password') }}" type="password" name="password" value="{{ old('password') }}" required>
                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback red-text" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="input-field">
                                        <input class="form-control white-text"  value="{{ old('password_confirmation') }}" placeholder="{{ __('Confirm Password') }}" type="password" name="password_confirmation" required>
                                </div>
                                <div class="input-field">
                                        <input class="form-control white-text"  value="{{ app('request')->input('ref_id') }}" placeholder="{{ __('Referral Id') }}" type="text" name="referral_id" required>
                                </div>
                                <div class="row my-4">
                                    <div class="col-12">
                                            <input class="custom-control-input" id="customCheckRegister" type="checkbox">
                                            <label class="custom-control-label" for="customCheckRegister">
                                </div>
                                    <div class="center">
                                        <button type="submit" class="waves-effect waves-light btn col s12 12 red"  style=" margin-bottom: 1rem;">Submit</button>
                                    </form>
                                    <p style=" margin-bottom: 1rem;">OR</p>
                                    <a href="/"
                                    class="waves-effect waves-light btn col s12 12 orange"  style=" margin-bottom: 1rem;">Login</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s1 m3"></div>
    </div>
    @endsection

