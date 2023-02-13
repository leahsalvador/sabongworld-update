<?php
/**
 * Project: tlp420
 * File: live.blade.php
 * Created: 11/17/22
 * Author: Abdullah Al Mamun <mamun1214@gmail.com>
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
                                    <h3 class=""><i class="fa fa-list"></i> Live Betting </h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" style="padding-top: 0.3rem;">
                            <div class="row">
                                <div class="col">
                                    <table
                                        class="table align-items-center table-bordered table-condensed table-striped"
                                        style="width:100%" id="betting">
                                        <thead>
                                        <tr>
                                            <th>Round</th>
                                            <th>Date</th>
                                            <th>Detail</th>
                                            <th>Current Points</th>
                                            <th>Amount</th>
                                            <th>Side</th>
                                            <th>Status</th>

                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($game_rounds as $gr)
                                                <?php
                                                $players_playings = $gr ? $gr->bettingHistory : [];
                                                ?>
                                                <?php foreach ($players_playings as $player_playing): ?>
                                            <tr>
                                                <td>Game #{{ @$gr->round }}</td>
                                                <td>{{ date('Y-m-d', strtotime($gr->created_at)) }}</td>
                                                <td>

                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <p class="m-0"><i
                                                                    class="fas fa-portrait    "></i>
                                                                {{ @$player_playing->player->name }}
                                                            </p>
                                                            <p class="m-0"><i
                                                                    class="fas fa-user"></i>
                                                                {{ @$player_playing->player->username }}
                                                            </p>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <p class="m-0"><i
                                                                    class="fas fa-address-book"></i>
                                                                {{ @$player_playing->player->email }}
                                                            </p>
                                                            <p class="m-0"><i
                                                                    class="fas fa-phone"></i>
                                                                {{ @$player_playing->player->phone_number }}
                                                            </p>
                                                        </div>

                                                    </div>
                                                </td>
                                                <td>{{ number_format(@$player_playing->player->wallet->points, 2, '.', ',') }}
                                                </td>
                                                <td>{{ number_format(@$player_playing->amount, 2, '.', ',') }}
                                                </td>
                                                <td>
                                                        <?php
                                                        if ($player_playing->side == 'tails') {
                                                            echo '<strong style="color:red;">WALA</strong>';
                                                        } else if ($player_playing->side == 'heads') {
                                                            echo '<strong style="color:blue;">MERON</strong>';
                                                        }
                                                        ?>
                                                </td>
                                                <td>
                                                    <strong>{{ @$player_playing->status == 'loose' ? 'LOSE' : strtoupper(@$player_playing->status) }}</strong>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
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
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            var bettingLogTable = $('#betting').DataTable({
                dom: 'Bfrtip',
                pageLength: 20,
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




