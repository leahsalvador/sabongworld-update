@extends('layouts.appAgent')

@section('content')
    @include('layouts.agentNavbars.headers.cards')
    <div class="container-fluid mt--7">
        <div class="container-fluid mt--6">
            <div class="row justify-content-center">
                <div class="col">
                    <div class="container-fluid" style="padding-top: 40px; background-color:white; border-radius: 5px">
                        <table class="table-striped table-responsive" style="width:100%" id="betting-log">
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
                            <?php foreach ($players_playings as $player_playing): ?>
                                <?php
                                $game_id = $player_playing->game_rounds_id;
                                $gr = App\Models\GameRound::where('id', $game_id)->first();
                                if (is_null($gr)) {
                                    continue;
                                }
                                ?>
                            <tr>
                                <td>Game #{{ $gr->round }}</td>
                                <td>{{ date('Y-m-d', strtotime($gr->created_at)) }}</td>
                                <td>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <p class="m-0"><i class="fas fa-portrait    "></i>
                                                {{ @$player_playing->player->name }}
                                            </p>
                                            <p class="m-0"><i class="fas fa-user"></i>
                                                {{ @$player_playing->player->username }}
                                            </p>
                                        </div>
                                        <div class="col-sm-6">
                                            <p class="m-0"><i class="fas fa-address-book"></i>
                                                {{ @$player_playing->player->email }}
                                            </p>
                                            <p class="m-0"><i class="fas fa-phone"></i>
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
                                            echo '<strong style="color:red;">MERON</strong>';
                                        } else if ($player_playing->side == 'heads') {
                                            echo '<strong style="color:blue;">WALA</strong>';
                                        }
                                        ?>
                                </td>
                                <td>
                                    <strong>{{ @$player_playing->status == 'loose' ? 'LOSE' : strtoupper(@$player_playing->status) }}</strong>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            @include('layouts.footers.auth')
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            var bettingLogTable = $('#betting-log').DataTable({
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
