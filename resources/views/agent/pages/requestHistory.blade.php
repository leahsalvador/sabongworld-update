@extends('layouts.appAgent')
@section('content')
    @include('layouts.agentNavbars.headers.cardsRequestHistory')
    <div class="container-fluid mt--7">
        <div class="container-fluid mt--6">
            <div class="row justify-content-center">
                <div class="col">
                    <div class="card">
                        <div class="card-header bg-transparent">
                            <div class="row mb-0">
                                <div class="col-sm-12 col-md-6">
                                    <h4><i class="fa fa-list"></i> PLAYER REQUEST HISTORY </h4>
                                </div>
                                {{--<div class="col-sm-12 col-md-6">
                                    <form method="GET" action="" class="float-right form-inline" accept-charset="UTF-8"
                                        id="search-transaction" role="form">
                                        <div class="form-group ">
                                            <select class="form-control col" name="username" id="username">
                                                <option value="" selected>
                                                    {{ @app('request')->input('username') ? app('request')->input('username') : 'Select a player' }}
                                                </option>
                                                @foreach ($players as $player)
                                                    <option value="{{ @$player->username }}">{{ @$player->username }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-primary" onclick="search()" style="z-index: 0;"
                                                type="button" id="button-addon2"><i class="fa fa-search"></i>
                                                Search</button>
                                        </div>
                                        <div class="input-group-append">
                                            <a class="btn btn-outline-danger" href="{{ url()->current() }}"
                                                style="z-index: 0;" type="button" id="button-addon2"><i
                                                    class="fa fa-ban"></i> Clear</a>
                                        </div>
                                    </form>
                                </div>--}}
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <div>
                                            <table class="table table-responsive align-items-center" id="example">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th scope="col" class="sort" data-sort="id">Date</th>
                                                        <th scope="col" class="sort" data-sort="details">Details</th>
                                                        <th scope="col" class="sort" data-sort="points">Username</th>
                                                        <th scope="col" class="sort" data-sort="date">Amount</th>
                                                        <th scope="col" class="sort" data-sort="date">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list">
                                                    @foreach ($request_histories as $request_history)
                                                        <tr>
                                                            <td class="details">
                                                                {{ @date_format($request_history->created_at, 'F d, Y') }}
                                                            </td>
                                                            <th scope="row">

                                                                <div class="row">
                                                                    <div class="col-sm-6">
                                                                        {{ @$request_history->details }}
                                                                    </div>
                                                                </div>
                                                            </th>
                                                            <td class="details">
                                                                {{ @$request_history->from->username }}
                                                            </td>
                                                            <td>
                                                                {{ number_format(@$request_history->amount, 2, '.', ',') }}
                                                            </td>
                                                            <td class="text-left">
                                                                @if ($request_history->transaction_status == 'success')
                                                                    <span class="badge badge-dot mr-4">
                                                                        <i class="bg-success"></i>
                                                                        {{ @$request_history->transaction_status }}
                                                                    </span>
                                                                @elseif ($request_history->transaction_status ==
                                                                    'pending')
                                                                    <span class="badge badge-dot mr-4">
                                                                        <i class="bg-warning"></i>
                                                                        {{ @$request_history->transaction_status }}
                                                                    </span>
                                                                @else
                                                                    <span class="badge badge-dot mr-4">
                                                                        <i class="bg-danger"></i>
                                                                        {{ @$request_history->transaction_status }}
                                                                    </span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    {{-- <nav aria-label="Page navigation example" class="float-right mt-3 ">
                                        <ul class="pagination">
                                            <li class="page-item {{ $current_page == 1 ? 'disabled ' : '' }}">
                                                <button class="page-link" onclick="search('{{ $current_page - 1 }}')"aria-label="Previous">
                                                    <i class="fas fa-angle-left"></i>
                                                    <span class="sr-only">Previous</span>
                                                </button>
                                            </li>
                                            @for ($i = 1; $i <= $total_page; $i++)
                                                <li class="page-item {{ $current_page == $i ? 'active' : '' }}">
                                                    <button class="page-link" onclick="search('{{ $i }}')">
                                                        {{ $i }}
                                                    </button>
                                            @endfor
                                            <li class="page-item {{ $current_page == $total_page ? 'disabled' : '' }}">
                                                <button class="page-link" class="page-link"
                                                    onclick="search('{{ $current_page + 1 }}')"
                                                    aria-label="Next">
                                                    <i class="fas fa-angle-right"></i>
                                                    <span class="sr-only">Next</span>
                                                </button>
                                            </li>
                                        </ul>
                                    </nav> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footers.auth')
    </div>
    @endsection @push('js')
    <script>
        function search(page = 1) {
            // create new URL object
            let username = document.getElementById('username').value;

            //    const url = new URL('{{ url()->full() }}');
            //     let link = parseParams(url.search)
            var search = location.search.substring(1);
            let link = {};
            if (search) {
                link = JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}', function(key,
                    value) {
                    return key === "" ? value : decodeURIComponent(value)
                })
                console.log(link);
            }
            let params = {
                page: page ? page : "1",
                username: username ? username : (link.username ? link.username : ''),
            }
            let seachParams = $.param(params)
            console.log(seachParams);
            window.location.href = `{{ url()->current() }}?${seachParams}`;
        }

    </script>
@endpush
