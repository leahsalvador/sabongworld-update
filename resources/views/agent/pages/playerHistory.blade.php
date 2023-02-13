<?php
/**
 * Created by Md.Abdullah Al Mamun.
 * Email: mamun1214@gmail.com
 * Date: 9/24/2022
 * Time: 12:04 AM
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
                            <div class="row m-0">
                                <div class="col-sm-6 col-md-6">
                                    <h3 class=""><i class="fa fa-history"></i> {{$user->username}}'s Histroy </h3>
                                </div>
                                <div class="col-sm-12 col-md-6">

                                </div>
                            </div>
                            <div class="table-responsive">
                                <div>
                                    <table class="table align-items-center table-bordered table-condensed table-striped" style="width:99%;" id="example">
                                        <thead class="thead-light">
                                        <tr>
                                            <th scope="col" class="sort" data-sort="id">#</th>
                                            <th scope="col" class="sort" data-sort="id">Date</th>
                                            <th scope="col" class="sort" data-sort="id">Amount</th>
                                            <th scope="col" class="sort" data-sort="details">Type</th>
                                            <th scope="col" class="sort" data-sort="details">Current Points</th>
                                            <th scope="col" class="sort" data-sort="points">Round #</th>
                                        </tr>
                                        </thead>
                                        <tbody class="list">
                                        @foreach ($logs as $key=>$log)
                                            <tr>
                                                <td>{{++$key}}</td>
                                                <td>
                                                    {{date_format(date_create($log->datetime),"F d,Y")}}
                                                    <br>
                                                    {{date_format(date_create($log->datetime),"h:i A")}}
                                                </td>
                                                <td>
                                                    @switch($log->type)
                                                        @case('win')
                                                        {{$log->win_amount}}
                                                        @break
                                                        @case('loose')
                                                        {{$log->loose_amount}}
                                                        @break
                                                        @case('draw')
                                                        0
                                                        @break
                                                        @case('deposit')
                                                        @case('system-deposit')
                                                        @case('withdraw')
                                                        @case('system-withdraw')
                                                        {{$log->amount}}
                                                        @break
                                                    @endswitch
                                                </td>
                                                <td>
                                                    {{$type[$log->type]}}
                                                </td>
                                                <td>
                                                    @switch($log->type)
                                                        @case('win')
                                                        {{$log->points + $log->win_amount - $log->amount}}
                                                        @break
                                                        @case('loose')
                                                        {{$log->points - $log->loose_amount}}
                                                        @break
                                                        @default
                                                        {{$log->points}}
                                                    @endswitch

                                                </td>
                                                <td>
                                                    @if(isset($log->round))
                                                        {{$log->round}}
                                                    @else
                                                        --
                                                    @endif
                                                </td>
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
