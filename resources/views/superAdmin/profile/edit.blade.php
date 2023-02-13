@extends('layouts.appSuperAdmin', ['title' => __('User Profile')])

@section('content')

    @if (auth()->user()->user_level == 'admin')
        @include('agent.users.partials.header', [
        'title' => __('Hello' . ' '. auth()->user()->name),
        'description' => __('This is your profile page. '),
        'class' => 'col-lg-12'
        ])
    @else
        @include('agent.users.partials.header', [
        'title' => __('Hello') . ' '. auth()->user()->name,
        'description' => __('This is your profile page. If you wish to change your personal info please contact customer
        service representative'),
        'class' => 'col-lg-7'
        ])
    @endif


    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-4 order-xl-2 mb-5 mb-xl-0">
                <div class="card card-profile shadow">
                    <div class="row justify-content-center">
                        <div class="col-lg-3 order-lg-2">
                            <div class="card-profile-image">
                                <a href="#">
                                    {{-- <img src="{{ asset('argon') }}/img/theme/team-4-800x800.jpg" class="rounded-circle"> --}}
                                    <img src="https://www.clipartmax.com/png/full/123-1237090_these-are-some-cats-avatar-i-drew-during-my-free-time-animated.png"
                                        class="rounded-circle">
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-header text-center border-0 pt-8 pt-md-4 pb-0 pb-md-4">
                        {{-- <div class="d-flex justify-content-between">
                            <a href="#" class="btn btn-sm btn-info mr-4">{{ __('Connect') }}</a>
                            <a href="#" class="btn btn-sm btn-default float-right">{{ __('Message') }}</a>
                        </div> --}}
                    </div>
                    <div class="card-body pt-0 pt-md-4">
                        <div class="row">
                            <div class="col">
                                <div class="card-profile-stats d-flex justify-content-center mt-md-5">
                                    <div>
                                        <span class="heading"> {{ auth()->user()->name }}</span>
                                        <span class="description">{{ auth()->user()->username }}</span>
                                    </div>
                                    {{-- <div>
                                        <span class="heading">{{ auth()->user()->phone_number }}</span>
                                        <span class="description"> {{ auth()->user()->email }}</span>
                                    </div> --}}
                                    {{-- <div>
                                        <span class="heading">89</span>
                                        <span class="description">{{ __('Comments') }}</span>
                                    </div> --}}
                                </div>
                            </div>
                        </div>

                </div>
            </div>
        </div>
        <div class="col-xl-8 order-xl-1">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <h3 class="mb-0">{{ __('Change Password') }}</h3>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('profile.password') }}" autocomplete="off">
                        @csrf
                        @method('put')

                        <h6 class="heading-small text-muted mb-4">{{ __('Password') }}</h6>

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
                                <label class="form-control-label"
                                    for="input-current-password">{{ __('Current Password') }}</label>
                                <input type="password" name="old_password" id="input-current-password"
                                    class="form-control form-control-alternative{{ $errors->has('old_password') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('Current Password') }}" value="" required>

                                @if ($errors->has('old_password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('old_password') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                <label class="form-control-label" for="input-password">{{ __('New Password') }}</label>
                                <input type="password" name="password" id="input-password"
                                    class="form-control form-control-alternative{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('New Password') }}" value="" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label class="form-control-label"
                                    for="input-password-confirmation">{{ __('Confirm New Password') }}</label>
                                <input type="password" name="password_confirmation" id="input-password-confirmation"
                                    class="form-control form-control-alternative"
                                    placeholder="{{ __('Confirm New Password') }}" value="" required>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-success mt-4">{{ __('Change password') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footers.auth')
    </div>
@endsection
