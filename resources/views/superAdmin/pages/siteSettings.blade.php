@extends('layouts.appSuperAdmin')

@section('content')
    @include('layouts.superAdminNavbars.headers.siteSettings')
    <div class="container-fluid mt--7">
        <div class="container-fluid mt--6">
            <div class="row justify-content-center">
                <div class="col">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="list-group">
                                        @foreach( $siteSettings as $siteSetting )
                                            <a class="list-group-item list-group-item-action flex-column align-items-start">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <form class="form-inline col-md-12" action="{{ route('superadmin-site-settings-save') }}"
                                                          method="POST">
                                                        @csrf
                                                        <div class="row col-md-8">
                                                            <div class="col-md-12">
                                                                <label class="text-dark" style="justify-content: flex-start;"
                                                                       for="inlineFormCustomSelectPref">{{ $siteSetting->name }}</label>
                                                                <input type="hidden" id="id" name="id" value="{{ $siteSetting->id }}">
                                                                <input type="text" id="result_controller" name="value" value="{{ $siteSetting->value }}" class="form-control" style="width: 90%!important;">
                                                                <button type="submit" class="btn btn-success">SAVE</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </a>
                                    @endforeach
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
        document.getElementById('bot_min_bet').addEventListener('change',e=>{
            console.log(document.getElementById('bot_min_bet').value);
            document.getElementById('total_min').textContent = parseInt(document.getElementById('bot_min_bet').value * 6).toLocaleString(
                    'en-US', {
                        maximumFractionDigits: 2
                    });
        });
        document.getElementById('bot_max_bet').addEventListener('change',e=>{
            console.log( document.getElementById('bot_max_bet').value);
            document.getElementById('total_max').textContent = parseInt( document.getElementById('bot_max_bet').value * 6).toLocaleString(
            'en-US', {
                maximumFractionDigits: 2
            });
        });

    </script>
    @endpush
