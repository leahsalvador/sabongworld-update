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
                            <div class="row m-0">
                                <div class="col-sm-6 col-md-6">
                                    <h3 class=""><i class="fa fa-list"></i> Commission Logs </h3>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive">
                                        <table class="table table-bordered table-condensed table-striped " id="commissions">
                                        <thead class="thead-light">
                                        <tr>
                                            <th scope="col" class="sort" data-sort="id">Date</th>
                                            <th scope="col" class="sort" data-sort="id">Fight #</th>
                                            <th scope="col" class="sort" data-sort="details">From</th>
                                            <th scope="col" class="sort" data-sort="details">To</th>
                                            <th scope="col" class="sort" data-sort="points">Amount</th>
                                        </tr>
                                        </thead>
                                        <tbody class="list">
                                        @foreach ($logs as $key=>$log)
                                            <tr>
                                                <td>
                                                    {{date_format(date_create($log->created_at),"F d,Y")}} {{date_format(date_create($log->created_at),"h:i A")}}
                                                </td>
                                                <td>{{$log->round}}</td>
                                                <td>{{Str::title(Str::replace("-"," ", $log->ftype))}} - {{Str::title($log->fname)}}</td>
                                                <td>{{Str::title(Str::replace("-"," ", $log->ttype))}} - {{Str::title($log->tname)}}</td>
                                                <td>{{$log->amount}}</td>
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
            <script>
                $(document).ready(function () {
                    var commissionTable = $('#commissions').DataTable({
                        dom: 'Bfrtip',
                        pageLength: 20,
                        stateSave: true,
                        order: [],
                        buttons: [
                            'excelHtml5',
                            'csvHtml5',
                            'pdfHtml5'
                        ]
                    });
                });
            </script>
        @endpush
    </div>
