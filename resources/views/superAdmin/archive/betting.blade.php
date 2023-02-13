<?php
/**
 * Created by Md.Abdullah Al Mamun.
 * Email: mamun1214@gmail.com
 * Date: 11/3/2022
 * Time: 8:25 PM
 * Year: 2022
 */
?>

@extends('layouts.appSuperAdmin')

@section('content')
    @include('layouts.superAdminNavbars.headers.cards')

    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-transparent" style="padding-bottom: 0.3rem;">
                        <div class="row m-0">
                            <div class="col-sm-6 col-md-6">
                                <h3 class=""><i class="fa fa-list"></i> Game Logs </h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="padding-top: 0.3rem;">
                        <div class="table-responsive">
                            <div style="margin-bottom: 15px;">
                                <table class="table align-items-center table-bordered table-condensed table-striped"
                                       id="games" style="width:99%;">
                                    <thead class="thead-light">
                                    <tr>
                                        <th scope="col" class="sort text-center" data-sort="name">Round #</th>
                                        <th scope="col" class="sort text-center" data-sort="budget">Player</th>
                                        <th scope="col" class="sort text-center" data-sort="status">Current Point</th>
                                        <th scope="col" class="text-center">Bet Amount</th>
                                        <th scope="col" class="sort text-center" data-sort="completion">Side</th>
                                        <th scope="col" class="text-center">Status</th>
                                        <th scope="col" class="text-center">Date</th>
                                    </tr>
                                    </thead>
                                    <tbody class="list"></tbody>
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
            $('#games').DataTable({
                dom: 'Bfrtip',
                searching: true,
                pageLength: 20,
                processing: true,
                serverSide: true,
                ordering: false,
                buttons: [
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5'
                ],
                ajax: $.fn.dataTable.pipeline({
                    url: "{{route('sadmin.archive.ajax.betting')}}",
                    pages: 3, // number of pages to cache
                }),
                columns: [
                    {"data": "round"},
                    {"data": "username"},
                    {"data": "current_points"},
                    {"data": "amount"},
                    {"data": "side"},
                    {"data": "status"},
                    {"data": "created_at"},
                ],
                columnDefs: [
                    {
                        responsivePriority: 1,
                        targets: 0,
                        // width: "100%",
                        render: function (data, type, full, meta) {
                            return "<strong>Game#" + data + "</strong>";
                        }
                    },
                    {
                        responsivePriority: 1,
                        targets: 4,
                        // width: "100%",
                        render: function (data, type, full, meta) {
                            switch (data) {
                                case 'heads':
                                    return '<strong style="color: blue; text-transform: uppercase;">WALA</strong>';
                                    break;
                                case 'tails':
                                    return '<strong style="color: brown; text-transform: uppercase;">MERON</strong>';
                                    break;
                            }
                        }
                    },
                    {
                        responsivePriority: 1,
                        targets: 5,
                        // width: "100%",
                        render: function (data, type, full, meta) {
                            switch (data) {
                                case 'win':
                                    return '<strong style="color: green; text-transform: uppercase;">win</strong>';
                                    break;
                                case 'loose':
                                    return '<strong style="color: red; text-transform: uppercase;">loose</strong>';
                                    break;
                                case 'ongoing':
                                    return '<strong style="color: dodgerblue; text-transform: uppercase;">ongoing</strong>';
                                    break;
                                case 'cancelled':
                                    return '<strong style="color: gray; text-transform: uppercase;">cancelled</strong>';
                                    break;
                                case 'draw':
                                    return '<strong style="color: skyblue; text-transform: uppercase;">draw</strong>';
                                    break;
                                case 'undo':
                                    return '<strong style="color: lightcoral; text-transform: uppercase;">undo</strong>';
                                    break;
                            }
                        }
                    }
                ],
            });
        });
    </script>
@endpush
