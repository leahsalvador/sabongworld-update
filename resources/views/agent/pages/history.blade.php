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
                                    <table
                                        class="table table-condensed table-striped table-hover align-items-center"
                                        id="example">
                                        <thead class="thead-light">
                                        <tr>
                                            <th scope="col" class="sort" data-sort="id">#</th>
                                            <th scope="col" class="sort" data-sort="date">Date</th>
                                            <th scope="col" class="sort" data-sort="amount">Amount</th>
                                            <th scope="col" class="sort" data-sort="type">Type</th>
                                            <th scope="col" class="sort" data-sort="from">From</th>
                                            <th scope="col" class="sort" data-sort="to">To</th>
                                            <th scope="col" class="sort" data-sort="points">Current Points</th>
                                            <th scope="col" class="sort" data-sort="status">Status</th>
                                            <th scope="col" class="sort" data-sort="details">Details</th>
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
                                                    {{$log->amount}}
                                                </td>
                                                <td>
                                                    {{$type[$log->type]}}
                                                </td>
                                                <td>
                                                    {{$log->from}}
                                                </td>
                                                <td>
                                                    {{$log->to}}
                                                </td>
                                                <td>
                                                    {{$log->points}}
                                                </td>
                                                <td>
                                                    {{$log->status}}
                                                </td>
                                                <td>
                                                    {{$log->details}}
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
        @endsection
        @push('js')
        @endpush
    </div>
