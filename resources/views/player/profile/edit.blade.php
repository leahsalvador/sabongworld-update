@extends('layouts.appPlayer', ['title' => __('User Profile')])

@section('content')

    @include('player.users.partials.header')
    <div class="container-fluid mt--7">
        <div class="col-xl-12 order-xl-1">
            <div class="card bg-secondary">
                <div class="card-body bg-default">
                    <form method="post" action="{{ route('profile.password') }}" autocomplete="off">
                        <h3 class="text-white">{{ __('Change Password') }}</h3>
                        @csrf
                        @method('put')
                        @if (session('password_status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('password_status') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="pl-lg-4">
                            <div class="form-group{{ $errors->has('old_password') ? ' has-danger' : '' }}">
                                <label class="form-control-label text-white"
                                    for="input-current-password">{{ __('Current Password') }}</label>
                                <input type="password" name="old_password" id="input-current-password"
                                    class="form-control text-white bg-default {{ $errors->has('old_password') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('Current Password') }}" value="" required>

                                @if ($errors->has('old_password'))
                                    <span class="text-red" role="alert">
                                        <strong>{{ $errors->first('old_password') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                <label class="form-control-label text-white"
                                    for="input-password">{{ __('New Password') }}</label>
                                <input type="password" name="password" id="input-password"
                                    class="form-control text-white bg-default {{ $errors->has('password') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('New Password') }}" value="" required>

                                @if ($errors->has('password'))
                                    <span class="text-red" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label class="form-control-label text-white"
                                    for="input-password-confirmation">{{ __('Confirm New Password') }}</label>
                                <input type="password" name="password_confirmation" id="input-password-confirmation"
                                    class="form-control form-control text-white bg-default"
                                    placeholder="{{ __('Confirm New Password') }}" value="" required>
                            </div>

                            <div class="text-center">
                                <button type="submit"
                                    class="btn btn-outline-success mt-1 float-right">{{ __('Change password') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @include('layouts.footers.auth')
    </div>
@endsection
