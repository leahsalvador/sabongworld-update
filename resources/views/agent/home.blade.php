@extends('layouts.appAgent')

@section('content')
    @include('layouts.agentNavbars.headers.cards')
    <div class="container-fluid mt--7">
        <div class="container-fluid mt--6">
            <div class="row justify-content-center">
                <div class="col">
                    <div class="row">

                        @if (auth()->user()->user_level == 'admin')
                            <div class="col-xl-6 col-lg-6">
                                <div class="card card-stats mb-4 mb-xl-0">
                                    @else
                                        <div class="col-xl-4 col-lg-4">
                                            <div class="card card-stats mb-4 mb-xl-0">
                                                @endif
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col">
                                                            <h5 class="card-title text-uppercase mb-0">
                                                                <div class="alert alert-warning  " role="alert">
                                                                    {{__('Please take note of your referral link below, All players that will
                                                                    register under this link will automatically be under your account.')}}
                                                                </div>
                                                            </h5>
                                                        </div>
                                                    </div>
                                                    <div class="input-group mb-3 mt-3">
                                                        <div class="input-group-prepend ">
                                                            <span class="input-group-text"
                                                                  id="inputGroup-sizing-default">{{__('Referral Link')}}</span>
                                                        </div>
                                                        <input type="text" id='referral-link' readonly
                                                               class="form-control" aria-label="Default"
                                                               aria-describedby="inputGroup-sizing-default"
                                                               value="{{ route('register') }}/{{ auth()->user()->code }}">
                                                    </div>
                                                    <button type="button" id="copy-link"
                                                            class="btn btn-outline-secondary"><i class="fas fa-link"
                                                                                                 onclick="copyLink()"></i> {{__('Copy Referral Link')}}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @if (auth()->user()->user_level != 'admin')
                                            <div class="col-xl-4 col-lg-4">
                                                <div class="card card-stats mb-4 mb-xl-0">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col">
                                                                <h5 class="card-title text-uppercase text-muted mb-0">{{__('Current Wallet')}}
                                                                </h5>
                                                                <span
                                                                    class="h2 font-weight-bold mb-0">{{ isset(auth()->user()->wallet->points) ? number_format(auth()->user()->wallet->points, 2, '.', ',') : 0 }}</span>
                                                            </div>
                                                            <div class="col-auto">
                                                                <div
                                                                    class="icon icon-shape bg-pink text-white rounded-circle shadow">
                                                                    <i class="fas fa-wallet"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <a href='{{ auth()->user()->user_level == 'admin' ? route('admin-agent-wallet') : route('agent-wallet') }}'
                                                           type="button" class="btn btn-outline-secondary mt-3 mb-0 "><i
                                                                class="fas fa-cogs"></i>
                                                            {{__('Manage Wallet')}}</a>

                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if (auth()->user()->user_level == 'admin')
                                            <div class="col-sm-6">
                                                <div class="card card-stats mb-4 mb-xl-0">
                                                    @else
                                                        <div class="col-xl-4 col-lg-4">
                                                            <div class="card card-stats mb-4 mb-xl-0">
                                                                @endif
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col">
                                                                            <h5 class="card-title text-uppercase text-muted mb-0">{{__('Current Comission')}}</h5>
                                                                            <span
                                                                                class="h2 font-weight-bold mb-0">{{ isset(auth()->user()->wallet->comission) ? number_format(auth()->user()->wallet->comission, 2, '.', ',') : 0 }}</span>
                                                                        </div>
                                                                        <div class="col-auto">
                                                                            <div class="icon icon-shape bg-info text-white rounded-circle shadow" style="padding: 6px;">
                                                                                <span style="font-size: 20px;" class="text-center text-white font-weight-bold">{{$commission}}<sup>%</sup></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    @if (auth()->user()->user_level != 'sub-agent')
                                                                        <a href="{{ auth()->user()->user_level == 'admin' ? route('admin-agent-comission') : route('agent-comission') }}"
                                                                           type="button"
                                                                           class="btn btn-outline-secondary mt-3 mb-0 "><i
                                                                                class="fas fa-cogs"></i>
                                                                            {{__('Manage Comission')}}</a>
                                                                    @endif

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
                            document.getElementById('copy-link').addEventListener('click', e => {
                                var copyText = document.getElementById("referral-link");
                                copyText.select();
                                copyText.setSelectionRange(0, 99999)
                                document.execCommand("copy");
                                // alert("Copied the text: " + copyText.value);
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Copied!',
                                    text: copyText.value,
                                    showConfirmButton: false,
                                    timer: 1500
                                })
                            });

                        </script>
    @endpush
