<?php
/**
 * Created by Md.Abdullah Al Mamun.
 * Email: mamun1214@gmail.com
 * Date: 9/20/2022
 * Time: 11:14 PM
 * Year: 2022
 */
?>
@extends('layouts.appAgent')
@section('content')
    @include('layouts.agentNavbars.headers.cards')
    <div class="container-fluid mt--7">
        <div class="container-fluid mt--6">
            <div class="row justify-content-center">
                <div class="col">
                    <div class="card">
                        <div class="card-header bg-transparent">
                            @if ($errors->has('password'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <span class="alert-icon"><i class="fa fa-exclamation-circle"></i></span>
                                    <strong>{{__('Error!')}}</strong> {{ $errors->first('password') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            @if (\Session::has('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                                    <span class="alert-text"><strong>{{__('Success!')}}</strong> {!! \Session::get('success') !!}</span>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            <div class="row m-0">
                                <div class="col-sm-6 col-md-6">
                                    <h3 class=""><i class="fa fa-list"></i> Transaction Histroy </h3>
                                </div>
                                {{--<div class="col-sm-12 col-md-6">
                                    <form method="GET" action="" class="float-right" accept-charset="UTF-8" role="form"
                                          class="form-inline">
                                        <div class="input-group input-group-sm mb-3">
                                            <input type="text" class="form-control" placeholder="Search Player"
                                                   value="{{ @app('request')->input('username') ?app('request')->input('username') :'' }}"
                                                   aria-label="Search Player" id="username" name="username"
                                                   aria-describedby="button-addon2">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-primary" onclick="search()"
                                                        style="z-index: 0;" type="button" id="button-addon2"><i
                                                        class="fa fa-search"></i> {{__('Search')}}</button>
                                            </div>
                                            <div class="input-group-append">
                                                <a class="btn btn-outline-danger"
                                                   href="{{ url('admin/clear-agent-list') }}"
                                                   style="z-index: 0;" type="button" id="button-addon2"><i
                                                        class="fa fa-ban"></i> {{__('Clear')}}</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>--}}
                            </div>
                            <div class="table-responsive">
                                <div>
                                    <table class="table table-responsive align-items-center " id="example">
                                        <thead class="thead-light">
                                        <tr>
                                            <th scope="col" class="sort" data-sort="id">#</th>
                                            <th scope="col" class="sort" data-sort="id">Datetime</th>
                                            <th scope="col" class="sort" data-sort="id">Transaction Type</th>
                                            <th scope="col" class="sort" data-sort="details">Name</th>
                                            <th scope="col" class="sort" data-sort="details">User Type</th>
                                            <th scope="col" class="sort" data-sort="points">commission rate</th>
                                            <th scope="col" class="sort" data-sort="points">commission Earned</th>
                                            <th scope="col" class="sort" data-sort="points">Cash in amount</th>
                                            <th scope="col" class="sort" data-sort="date">Cashout amount</th>
                                            <th scope="col">Current Points</th>
                                        </tr>
                                        </thead>
                                        <tbody class="list">
                                        @foreach ($logs as $key=>$log)
                                            <tr>
                                                <td>{{++$key}}</td>
                                                <td>
                                                    {{date_format(date_create($log->created_at),"F d,Y")}}
                                                    <br>
                                                    {{date_format(date_create($log->created_at),"h:i A")}}
                                                </td>
                                                <td>{{$log->transaction_type}}</td>
                                                <td>{{$log->name}}</td>
                                                <td>{{$log->user_type}}</td>
                                                <td>{{$log->commission_percent}}</td>
                                                <td>{{$log->comission}}</td>
                                                <td>
                                                    @if(in_array($log->transaction_type,['deposit','agent-deposit','system-deposit']))
                                                        {{$log->amount}}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(in_array($log->transaction_type,['withdraw','agent-withdraw','system-withdraw']))
                                                        {{$log->amount}}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{$log->points}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        @include('layouts.footers.auth')
        @endsection @push('js')
        @endpush
    </div>
