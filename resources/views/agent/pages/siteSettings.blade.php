@extends('layouts.appAgent')

@section('content')
    @include('layouts.agentNavbars.headers.siteSettings')
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
                                                <form class="form-inline" action="{{ route('site-settings-save') }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col">
                                                            <label class="text-dark" style="justify-content: flex-start;"
                                                                for="inlineFormCustomSelectPref">{{ $siteSetting->name }}</label>
                                                            <input type="text" id="id" name="id" hidden
                                                                value="{{ $siteSetting->id }}">
                                                            <input type="text" id="result_controller" name="value"
                                                                    value="{{ $siteSetting->value }}" class="form-control ">
                                                                    <button type="submit" class="btn btn-sm btn-success mb-2">SAVE</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </a>
                                        @endforeach
                                        {{--<a class="list-group-item list-group-item-action flex-column align-items-start">--}}
                                            {{--<div class="d-flex w-100 justify-content-between">--}}
                                                {{--<form class="form-inline" action="{{ route('site-settings-save') }}"--}}
                                                    {{--method="POST" enctype="multipart/form-data">--}}
                                                    {{--@csrf--}}
                                                    {{--<div class="row">--}}
                                                        {{--<div class="col">--}}
                                                            {{--<input type="hidden" name="upload_type" value="1"/>--}}
                                                            {{--<label class="text-dark" style="justify-content: flex-start;"--}}
                                                                {{--for="inlineFormCustomSelectPref">video uploading</label>--}}
                                                            {{--<input type="text" id="id" name="id" hidden--}}
                                                                {{-->--}}
                                                            {{--<input type="file" id="result_controller" name="value" class="form-control " required>--}}
                                                            {{--<button type="submit" class="btn btn-sm btn-success mb-2" style="">SAVE</button>--}}
                                                            {{--<br/>--}}
                                                            {{--<h5 class="mt-4">Current Image:</h5>--}}
                                                        {{--</div>--}}
                                                    {{--</div>--}}
                                                {{--</form>--}}
                                            {{--</div>--}}
                                        {{--</a>--}}

                                        {{--<a class="list-group-item list-group-item-action flex-column align-items-start">--}}
                                            {{--<div class="d-flex w-100 justify-content-between">--}}
                                                {{--<form class="form-inline" action="{{ route('upload-betting-video') }}"--}}
                                                      {{--method="POST" enctype="multipart/form-data">--}}
                                                    {{--@csrf--}}
                                                    {{--<div class="row">--}}
                                                        {{--<div class="col">--}}
                                                            {{--<input type="hidden" name="upload_type" value="1"/>--}}
                                                            {{--<label class="text-dark" style="justify-content: flex-start;"--}}
                                                                   {{--for="inlineFormCustomSelectPref">video uploading</label>--}}
                                                            {{--<input type="text" id="id" name="id" hidden--}}
                                                            {{-->--}}
                                                            {{--<input type="file" id="result_controller" name="video" class="form-control " required>--}}
                                                            {{--<button type="submit" class="btn btn-sm btn-success mb-2" style="">SAVE</button>--}}

                                                            {{--<br/>--}}
                                                            {{--<h5 class="mt-4">Real Video File:</h5>--}}
                                                        {{--</div>--}}
                                                    {{--</div>--}}
                                                {{--</form>--}}
                                            {{--</div>--}}
                                        {{--</a>--}}




                                        <!--
                                        
                                        <a class="list-group-item list-group-item-action flex-column align-items-start">
                                            <div class="d-flex w-100 justify-content-between">
                                                <form class="form-inline" action="{{ route('site-settings-save') }}"
                                                    method="POST"  enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col">
                                                            <input type="hidden" name="upload_type" value="1"/>
                                                            <label class="text-dark" style="justify-content: flex-start;"
                                                                for="inlineFormCustomSelectPref">{{ $siteSettings[0]->name }}</label>
                                                            <input type="text" id="id" name="id" hidden
                                                                value="{{ $siteSettings[0]->id }}">
                                                            <input type="file" id="result_controller" name="value" class="form-control" accept="image/*" required>
                                                                    <button type="submit" class="btn btn-sm btn-success mb-2">SAVE</button>
                                                            <br/>
                                                            <h5 class="mt-4">Current Image:</h5>
                                                            <img src="{{ asset($siteSettings[0]->value) }}" style="width: 50%;" />
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </a> -->
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
        /*document.getElementById('bot_min_bet').addEventListener('change',e=>{
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
        });//*/

    </script>
    @endpush
