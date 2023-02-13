@extends('layouts.appSuperAdmin', ['title' => __('User Settings')])

@section('content')
    @if (auth()->user()->user_level == 'admin')
        @include('agent.users.partials.header', [
        'title' => __('Hello') . ' '. auth()->user()->name,
        'description' => __('This is your profile page.'),
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

                    </div>
                    <div class="card-body pt-0 pt-md-4">
                        <div class="row">
                            <div class="col">
                                <div class="card-profile-stats d-flex justify-content-center mt-md-5">
                                    <div>
                                        <span class="heading"> {{ auth()->user()->name }}</span>
                                        <span class="description">{{ auth()->user()->username }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                       
                    </div>
                </div>
            </div>
            <div class="col-xl-8 order-xl-1">
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        @if (\Session::has('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                                <span class="alert-text"><strong>Success!</strong> {!! \Session::get('success') !!}</span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        @if (auth()->user()->user_level != 'admin')
                            <h3 class="mb-0"> Settings <small>This information is only for us</small>
                            </h3>
                            <h5>
                                <strong class="form-text text-danger">CONTACT CSR TO CHANGE ACCOUNT NAME.</strong>
                            </h5>
                            @else
                            <h4> Profile Settings</h4>
                        @endif
                    </div>
                    <div class="card-body">
                        @if (auth()->user()->user_level != 'admin')

                            <div class="form-group">
                                <label for="first_name">Full Name</label>
                                <input type="text" name="first_name" id="first_name" value="{{ auth()->user()->name }}"
                                    placeholder="First Name" autofocus="autofocus" disabled="disabled" required="required"
                                    class="form-control">
                            </div>
                            <hr>


                        @endif
                        <form method="post" action="{{ route('profile.settingsUpdate') }}" autocomplete="off">
                            @csrf
                            @method('put')

                            <div class="form-group">
                                <label for="contact_person">Contact Person</label>
                                <input type="text"
                                    value="{{ auth()->user()->agent_details ? auth()->user()->agent_details->contact_person : '' }}"

                                    name="contact_person" id="contact_person" placeholder="Enter Contact Person"
                                    required="required" class="form-control">
                            </div>

                            <div class="form-group"><label for="phone_number">Phone Number with <i class="fab fa-viber"></i>
                                    <strong>VIBER</strong></label>
                                <input type="number" name="phone_number" id="phone_number"
                                    value="{{ auth()->user()->agent_details ? auth()->user()->agent_details->phone_number : auth()->user()->phone_number }}"
                                    placeholder="Enter phone with viber" required="required" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="address">Complete Address</label>
                                <textarea name="address" id="address" rows="4" placeholder="Enter your complet address"

                                    class="form-control">{{ auth()->user()->agent_details ? auth()->user()->agent_details->address : '' }}</textarea>

                            </div>
                            <div class="form-group">
                                <label for="details">BANK ACCT. DETAILS</label>
                                <textarea name="details" id="details" rows="4" placeholder="Enter your bank account details"

                                    class="form-control">{{ auth()->user()->agent_details ? auth()->user()->agent_details->details : '' }}</textarea>

                            </div>
                            <h4> Other Settings <small>This will be shown to your player's betting console</small>
                            </h4>
                            <div class="form-group"><label for="player_phone_number">Phone Number</label>

                                <input type="number" name="player_phone_number"
                                    value="{{ auth()->user()->agent_details ? auth()->user()->agent_details->player_phone_number : '' }}"
                                    id="player_phone_number" placeholder="Enter phone number" required="required"
                                    class="form-control">

                                <small id="emailHelp" class="form-text text-muted">Please enter phone number where your
                                    players can contact you.</small>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="basic-url">Facebook</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon3">https://facebook.com/</span>
                                    </div>

                                    <input type="text"
                                        value="{{ auth()->user()->agent_details ? auth()->user()->agent_details->facebook_link : '' }}"
                                        name="facebook_link" id="facebook_link" class="form-control"

                                        aria-describedby="basic-addon3">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">SAVE SETTINGS</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footers.auth')
    </div>
@endsection
