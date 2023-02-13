@extends('layouts.appAgent')
@section('content')
    @include('layouts.agentNavbars.headers.cardsWallet')
    <div class="container-fluid mt--7">
        <div class="container-fluid mt--6">
            <div class="row justify-content-center">
                <div class="col">
                    <div class="card">
                        <div class="card-header bg-transparent">

                            @if (\Session::has('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                                    <span class="alert-text"><strong>Success!</strong> {!! \Session::get('success') !!}</span>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                                @if (\Session::has('error'))
                                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                        <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                                        <span class="alert-text"><strong>Error!</strong> {!! \Session::get('error') !!}</span>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                            <h3 class="mb-0">
                                <i class="fa fa-money-check-alt"></i>Wallet Loading Station
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-12 col-lg-12">
                                    <div class="card card-stats border-dark">
                                        <div class="card-body">
                                            <div id="loading_form_body" class="card-body">
                                                <form
                                                    action="{{ auth()->user()->user_level == 'admin' ? route('admin-agent-wallet-modify') : route('agent-wallet-modify') }}"
                                                    id="wallet-form" method="POST" onsubmit="hideModal()">
                                                    @csrf
                                                    <div class="form-group">
                                                        <label for="company"><strong>Transaction Type</strong></label>
                                                        <select id="transaction_type" required="required"
                                                            name="transaction_type" class="form-control">
                                                            <option selected="selected" value="">Select a transaction type
                                                            </option>
                                                            <option value="deposit">Deposit</option>
                                                            <option value="withdraw">Withdraw</option>
                                                        </select>
                                                        @if ($errors->has('transaction_type'))
                                                            <span class="invalid-feedback red-text" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $errors->first('transaction_type') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-sm-6">
                                                            <label><strong>Load To</strong></label>
                                                            <select name="load_to" id="load-to" required="required"
                                                                placeholder="Select a username" style="width: 100%;">
                                                                <option value="">select a username</option>
                                                                @foreach ($users as $user)
                                                                    <option value="{{ @$user->id }}"
                                                                        id="{{ @$user->wallet->points }},{{ @$user->name }}"
                                                                        class="text-info">
                                                                        {{ @$user->username }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @if ($errors->has('load_to'))
                                                                <span class="invalid-feedback red-text"
                                                                    style="display: block;" role="alert">
                                                                    <strong>{{ $errors->first('load_to') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="form-group col-sm-6">
                                                            <label for="street"><strong>Amount</strong></label>

                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text form-control-sm">₱</span>
                                                                </div>
                                                                <input id="amount" placeholder="Enter Amount"
                                                                    required="required" name="amount" type="text"
                                                                    class="form-control form-control-sm" />
                                                            </div>
                                                            @if ($errors->has('amount'))
                                                                <span class="invalid-feedback red-text"
                                                                    style="display: block;" role="alert">
                                                                    <strong>{{ $errors->first('amount') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-sm-12">
                                                            <label for="details"><strong>Details</strong></label>
                                                            <input placeholder="Enter Details" id="details" name="details"
                                                                type="text" class="form-control" />
                                                            <div class="invalid-feedback"></div>
                                                            @if ($errors->has('details'))
                                                                <span class="invalid-feedback red-text"
                                                                    style="display: block;" role="alert">
                                                                    <strong>{{ $errors->first('details') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="card-footer">
                                                <button type="submit" class="btn btn btn-outline-success float-right">
                                                    <i class="fa fa-save"></i>
                                                    Submit
                                                </button>
                                                @if ($errors->has('password'))
                                                    <span class="invalid-feedback red-text" style="display: block;"
                                                        role="alert">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                @endif
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
        @endsection @push('js')

        <script>
             $('#load-to').select2({theme: "bootstrap"});
             $('#load-to').addClass('form-control select2-single form-control-sm')
             $('#load-to').on('select2:select', function (e) {
                var data = e.params.data.element.id;
                data = data.split(',');
                document.getElementById('user-wallet-name').textContent = data[1];
                document.getElementById('user-wallet-points').textContent = parseInt(data[0]).toLocaleString(
                    'en-US', {
                        maximumFractionDigits: 2
                    });
                console.log(data);
            });
            document.getElementById('load-to').addEventListener('change', function() {
                let data = this.options[this.selectedIndex].id;
                data = data.split(',');
                document.getElementById('user-wallet-name').textContent = data[1];
                document.getElementById('user-wallet-points').textContent = parseInt(data[0]).toLocaleString(
                    'en-US', {
                        maximumFractionDigits: 2
                    });
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
