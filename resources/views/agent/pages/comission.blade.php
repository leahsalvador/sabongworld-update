@extends('layouts.appAgent') @section('content') @include('layouts.agentNavbars.headers.cardsComission')
    <div class="container-fluid mt--7">
        <div class="container-fluid mt--6">
            <div class="row justify-content-center">
                <div class="col">
                    <div class="card">
                        <div class="card-header bg-transparent">

                            @if (\Session::has('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                                    <span class="alert-text"><strong>{{__('Success!')}}</strong> {!! \Session::get('success') !!}</span>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            <h3 class="mb-0">
                                <i class="fa fa-money-check-alt"></i>{{__('Commission Loading Station')}}
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-12 col-lg-12">
                                    <div class="card card-stats border-dark">
                                        <div class="card-body">
                                            <form action="{{ auth()->user()->user_level == 'admin' ? route('admin-agent-comission-modify') : route('agent-comission-modify') }}" id="wallet-form"
                                                method="POST" onsubmit="hideModal()">
                                                @csrf
                                                <div id="loading_form_body" class="card-body">
                                                    <div class="form-group"><label for="company"><strong>{{__('Transaction Type')}}</strong></label> <select id="transaction_type"
                                                            required="required" name="transaction_type"
                                                            class="form-control">
                                                            <option value="">{{__('Select a transaction type')}}</option>
                                                            <option value="withdraw" selected="selected">{{__('Agent Commission Cashout')}}</option>
                                                        </select>
                                                        @if ($errors->has('transaction_type'))
                                                        <span class="invalid-feedback red-text" style="display: block;"
                                                            role="alert">
                                                            <strong>{{ $errors->first('transaction_type') }}</strong>
                                                        </span>
                                                    @endif
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-sm-6"><label><strong
                                                                    id="loadUsernameLabel">{{__('Cashout From')}}</strong></label>
                                                            <select name="load_to" id="load-to" required="required"
                                                                placeholder="Select a username" style="width: 100%;">
                                                                <option value="">{{__('Select Agent')}}</option>
                                                                @foreach ($users as $user)
                                                                    <option value="{{ @$user->id }}"
                                                                        id="{{ @$user->wallet->comission }},{{ @$user->name }}"
                                                                        class="text-info">
                                                                        {{ @$user->username }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @if ($errors->has('load_to'))
                                                        <span class="invalid-feedback red-text" style="display: block;"
                                                            role="alert">
                                                            <strong>{{ $errors->first('load_to') }}</strong>
                                                        </span>
                                                    @endif
                                                        </div>
                                                        <div class="form-group col-sm-6">
                                                            <label for="street"><strong>{{__('Amount')}}</strong></label>

                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text form-control-sm">₱</span>
                                                                </div>
                                                                <input id="amount" placeholder="Enter Amount"
                                                                    required="required" name="amount" type="text"
                                                                    class="form-control form-control-sm" />
                                                            </div>
                                                            @if ($errors->has('amount'))
                                                            <span class="invalid-feedback red-text" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $errors->first('amount') }}</strong>
                                                            </span>
                                                        @endif
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-sm-12"><label
                                                                for="details"><strong>{{__('Details')}}</strong></label> <input
                                                                placeholder="Enter Details"  id="details"
                                                                name="details" type="text" class="form-control ">
                                                            <div class="invalid-feedback"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <button type="button" class="btn btn btn-outline-success float-right"
                                                        data-toggle="modal" data-target="#modal-notification">
                                                        <i class="fa fa-save"></i>
                                                        {{__('Submit')}}
                                                    </button>
                                                    @if ($errors->has('password'))
                                                        <span class="invalid-feedback red-text" style="display: block;"
                                                            role="alert">
                                                            <strong>{{ $errors->first('password') }}</strong>
                                                        </span>
                                                    @endif
                                                    <div class="col-md-4">
                                                        <div class="modal fade" id="modal-notification" tabindex="-1"
                                                            role="dialog" aria-labelledby="modal-notification"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog modal-danger modal-dialog-centered"
                                                                role="document">
                                                                <div class="modal-content bg-gradient-danger">

                                                                    <div class="modal-header">
                                                                        <h6 class="modal-title"
                                                                            id="modal-title-notification">
                                                                        </h6>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">×</span>
                                                                        </button>
                                                                    </div>

                                                                    <div class="modal-body">

                                                                        <div class="py-3 text-center">
                                                                            <span style="font-size: 5rem;">
                                                                                <i class="fas fa-lock"></i>
                                                                            </span>
                                                                            <h4 class="heading mt-4">{{__('Please Input your password for confirmation')}}</h4>
                                                                            <div class="form-group col-sm-12">
                                                                                <div class="input-group">
                                                                                    <input id="password"
                                                                                        placeholder="Enter password"
                                                                                        required="required" name="password"
                                                                                        type="password"
                                                                                        class="form-control" />
                                                                                </div>
                                                                                @if ($errors->has('password'))
                                                                                    <span
                                                                                        class="invalid-feedback text-white"
                                                                                        style="display: block;"
                                                                                        role="alert">
                                                                                        <strong>{{ $errors->first('password') }}</strong>
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                    </div>

                                                                    <div class="modal-footer">
                                                                        <button type="button"
                                                                            class="btn btn-link text-white ml-auto"
                                                                            data-dismiss="modal">{{__('Close')}}</button>
                                                                        <button type="submit" class="btn btn-white"><i
                                                                                class="fa fa-save"></i> {{__('Submit')}}</button>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.footers.auth')
        </div>
        @endsection
        @push('js')
        <script>
            $('#load-to').select2({theme: "bootstrap"});
             $('#load-to').addClass('form-control select2-single form-control-sm')
             $('#load-to').on('select2:select', function (e) {
                var data = e.params.data.element.id;
                data = data.split(',');
                document.getElementById('agent-comission-name').textContent = data[1];
                document.getElementById('agent-comission-points').textContent = parseInt(data[0]).toLocaleString(
                    'en-US', {
                        maximumFractionDigits: 2
                    });
                console.log(data);
            });
            document.getElementById('load-to').addEventListener('change', function() {
                let data = this.options[this.selectedIndex].id;
                data = data.split(',');
                document.getElementById('agent-comission-name').textContent = data[1];
                document.getElementById('agent-comission-points').textContent = data[0];
                console.log(data);
            });
            function hideModal() {
                $('#modal-notification').modal('hide');
                let timerInterval
                Swal.fire({
                    title: 'Success!',
                    timer: 2000,
                    icon: 'success',
                    timerProgressBar: false,
                    didOpen: () => {},
                    willClose: () => {
                        clearInterval(timerInterval)
                    }
                }).then((result) => {

                })

            }
        </script>
    @endpush
</div>
