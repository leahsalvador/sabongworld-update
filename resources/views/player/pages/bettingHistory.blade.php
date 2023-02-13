@extends('layouts.appPlayer')
@section('content')
    @include('layouts.playerNavbars.headers.cards')

    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-7 mt-3">
                <div class="card bg-default border-light">
                    <div class="card-header bg-default">
                        <label for="exampleFormControlSelect1 bg-default" class="mb-1 text-white">Betting
                            History</label>
                        <label for="exampleFormControlInput1" class="float-right text-white">Current Points :
                            <strong style="color: #ffdc11">
                                {{ number_format(auth()->user()->wallet->points, 2, '.', ',') }}</strong></label>
                    </div>
                    <div class="card-body">
                        <table class="table table-responsive">
                            <thead>
                            <tr>
                                <th class="text-white">Date</th>
                                <th class="text-white">Round</th>
                                <th class="text-white">Status</th>
                                <th class="text-white">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($betting_logs as $betting_logs)
                                <tr>

                                    <td class="text-white">
                                        {{ @date_format($betting_logs->created_at, 'F d, Y') }}
                                    </td>
                                    <td class="text-white">{{ @$betting_logs->round }}</td>
                                    <td class="text-white">
                                        @switch(@$betting_logs->status )
                                            @case('done')
                                            <span class="badge badge-dot mr-4">
                                                    <i class="bg-success"></i>
                                                    <span class="status">Done</span>
                                                </span>
                                            @break
                                            @case('closed')
                                            <span class="badge badge-dot mr-4">
                                                    <i class="bg-success"></i>
                                                    <span class="status">Closed</span>
                                                </span>
                                            @break
                                            @case('cancelled')
                                            <span class="badge badge-dot mr-4">
                                                    <i class="bg-danger"></i>
                                                    <span class="status">Cancelled</span>
                                                </span>
                                            @break
                                            @default
                                            <span class="badge badge-dot mr-4">
                                                    <i class="bg-danger"></i>
                                                    <span class="status"></span>
                                                </span>
                                        @endswitch
                                    </td>
                                    <td class="text-white">
                                        <input type="text" hidden value="{{ @$betting_logs->id }}">
                                        <button class="btn btn-outline-warning btn-sm"
                                                onClick="viewDetails('{{ @$betting_logs->id }}','{{ @$betting_logs->round }}')">
                                            View
                                            Details
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <nav aria-label="..." style="overflow: auto;">
                            <ul class="pagination mb-0">
                                <li class="page-item  {{ $current_page == 1 ? 'disabled ' : '' }}">
                                    <a class="page-link bg-default text-white"
                                       href="{{ url()->current() . '?page=' . ($current_page - 1) }}"
                                       aria-label="Previous">
                                        <i class="fas fa-angle-left"></i>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                </li>
                                @for ($i = 1; $i <= $total_page; $i++)
                                    <li class="page-item  {{ $current_page == $i ? 'active' : '' }}">
                                        <a class="page-link bg-default text-white"
                                           href="{{ url()->current() . '?page=' . $i }}">
                                            {{ $i }}
                                        </a>
                                    </li>
                                @endfor
                                <li class="page-item  {{ $current_page == $total_page ? 'disabled' : '' }}">
                                    <a class="page-link bg-default text-white" class="page-link"
                                       href="{{ url()->current() . '?page=' . ($current_page + 1) }}" aria-label="Next">
                                        <i class="fas fa-angle-right"></i>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-5 mt-3">
                <div class="bg-default border-light card">
                    <div class="card-header bg-default">
                        <label for="exampleFormControlSelect1 bg-default" class="mb-1 text-white">Round Details</label>
                    </div>
                    <div class="card-body" id="details-bet">

                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth')
    </div>
@endsection

@push('js')
    <script>
        function viewDetails(game_id, round) {
            fetch('bet_details/' + game_id)
                .then(response => response.json())
                .then(data => {
                    const options = {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    };
                    // console.log(data.data[0]);
                    const event = new Date(data.data[0].created_at);
                    let betDate = event.toLocaleDateString(undefined, options);
                    document.getElementById('details-bet').innerHTML = `<span class="h2 text-white">Round <strong class="text-danger">#${round}</strong> of  ${betDate}</span>`;
                    data.data.forEach(element => {
                        let current_win = parseFloat(element.current_points) + parseFloat(element.win_amount);
                        let current_loose = parseFloat(element.current_points) - parseFloat(element.amount) < 0 ? 0 : parseFloat(element.current_points) - parseFloat(element.amount);
                        document.getElementById('details-bet').innerHTML += `<ul class="list-group bg-default mb-2">
                            <li class="list-group-item bg-default"><span class="h5 text-white mb-0">Side : <strong
                                        class="text-info">${element.side == 'tails' ? 'WALA' : 'MERON'}</strong></span></li>
                            <li class="list-group-item bg-default"><span class="h5 text-white mb-0">Status: <strong
                                        class="${element.status == 'win' ? 'text-success' : 'text-danger'}">${element.status == 'win' ? 'Won' : 'Lost'}</strong></span></li>
                            <li class="list-group-item bg-default"><span class="h5 text-white mb-0">Initial Points: <strong
                                        class="text-yellow">${element.current_points ? parseFloat(element.current_points).toLocaleString('en-US', {maximumFractionDigits: 2}) : 0}</strong></span></li>
                            <li class="list-group-item bg-default"><span class="h5 text-white mb-0">Total Bet : <strong
                                        class="text-info">${parseFloat(element.amount).toLocaleString('en-US', {maximumFractionDigits: 2})}</strong></span></li>
                            <li class="list-group-item bg-default"><span class="h5 text-white mb-0">Total Win : <strong
                                        class="text-success">${element.status == 'win' ? parseFloat(element.win_amount).toLocaleString('en-US', {maximumFractionDigits: 2}) : 0}</strong></span></li>
                            <li class="list-group-item bg-default"><span class="h5 text-white mb-0">Total Lost : <strong
                                        class="text-danger">${element.status == 'loose' ? parseFloat(element.amount).toLocaleString('en-US', {maximumFractionDigits: 2}) : 0}</strong></span></li>
                        </ul>`;
                    });
                    // <li class="list-group-item bg-default"><span class="h5 text-white mb-0">Points After : <strong
                    //             class="text-yellow">${element.status == 'win' ? current_win.toLocaleString('en-US', {maximumFractionDigits:2}) : current_loose.toLocaleString('en-US', {maximumFractionDigits:2}) }</strong></span></li>
                });
        }

    </script>
@endpush
