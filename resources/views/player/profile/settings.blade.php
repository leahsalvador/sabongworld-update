@extends('layouts.appPlayer', ['title' => __('User Settings')])

@section('content')
    @include('player.users.partials.header')
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card bg-default border-light">
                    <div class="card-body">
                        @if (\Session::has('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                                <span class="alert-text"><strong>Success!</strong> {!! \Session::get('success') !!}</span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <h5>
                            <strong class="form-text text-danger">CONTACT CSR TO CHANGE ACCOUNT NAME.</strong>
                        </h5>
                        <form method="post" action="{{ route('player.settingsUpdate') }}" autocomplete="off">
                            @csrf
                            @method('put')

                            <div class="form-group">
                                <label for="first_name" class="text-white">Full Name</label>
                                <input type="text" name="first_name" id="first_name" value="{{ auth()->user()->name }}"
                                    placeholder="First Name" autofocus="autofocus" disabled="disabled" required="required"
                                    class="form-control text-white bg-default">
                            </div>
                            <div class="form-group text-white"><label for="phone_number">Phone Number</label>
                                <input type="number" name="phone_number"
                                    value="{{ auth()->user() ? auth()->user()->phone_number : '' }}" id="phone_number"
                                    placeholder="Enter phone number" required="required"
                                    class="form-control text-white bg-default {{ $errors->has('phone_number') ? ' is-invalid' : '' }}">

                            </div>
                            @if ($errors->has('phone_number'))
                                <span class="text-red" role="alert">
                                    <strong>{{ $errors->first('phone_number') }}</strong>
                                </span>
                            @endif
                            <div class="form-group">
                                <label class="form-control-label text-white" for="basic-url">Facebook</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-default text-white"
                                            id="basic-addon3">https://facebook.com/</span>
                                    </div>

                                    <input type="text" value="{{ auth()->user() ? auth()->user()->facebook_link : '' }}"
                                        name="facebook_link" id="facebook_link" class="form-control text-white bg-default {{ $errors->has('facebook_link') ? ' is-invalid' : '' }}"
                                        aria-describedby="basic-addon3" required>
                                </div>

                                @if ($errors->has('facebook_link'))
                                    <span class="text-red" role="alert">
                                        <strong>{{ $errors->first('facebook_link') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-outline-success bt-1 float-right">SAVE SETTINGS</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footers.auth')
    </div>
@endsection
