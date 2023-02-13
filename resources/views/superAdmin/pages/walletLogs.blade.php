@extends('layouts.appSuperAdmin')
@section('content')
    @include('layouts.superAdminNavbars.headers.cardsWalletLogs')
    <div class="container-fluid mt--7">
        <div class="container-fluid mt--6">
            <div class="row justify-content-center">
                <div class="col">
                    <div class="card">
                        <div class="card-header bg-transparent">
                            <div class="row">
                                <div class="col-sm-12 col-md-12">
                                    <div class="col-sm-12">
                                        <form class="form-inline float-right">
                                            <div class="form-group">
                                                <select id="transaction-type" name="transaction_type" class="form-control">
                                                    <option value="" selected="selected">
                                                        {{ @app('request')->input('transaction_type') ? app('request')->input('transaction_type') : 'Filter Transaction Type' }}
                                                    </option>
                                                    <option value="deposit">Deposit</option>
                                                    <option value="withdraw">Withdrawal</option>
                                                    <option value="agent-deposit">Agent Deposit</option>
                                                    <option value="agent-withdraw">Agent Withdrawal</option>
                                                    <option value="system-deposit">System Deposit</option>
                                                    <option value="system-withdraw">System Withdrawal</option>
                                                </select>&nbsp;
                                            </div>
                                            <div class="form-group">
                                                <select name="username" id="username" required="required"
                                                    placeholder="Select a username"
                                                    class="form-control username-select2 select2-hidden-accessible"
                                                    tabindex="-1" aria-hidden="true">
                                                    <option value="">
                                                        {{ @app('request')->input('username') ? app('request')->input('username') : 'Select a username' }}
                                                    </option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ @$user->name }}" class="text-info">
                                                            {{ @$user->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group mr-2">&nbsp; <button class="btn btn-outline-primary"
                                                    style="z-index: 0;" onclick="search()" type="button"
                                                    id="button-addon2"><i class="fa fa-search"></i> Search</button></div>
                                            <div class="form-group mr-2"> <a class="btn btn-outline-danger"
                                                    style="z-index: 0;" href="{{ url()->current() }}"
                                                    id="button-addon2"><i class="fa fa-ban"></i> Clear</a></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="card card-stats">
                                <div class="card-header"><i class="fa fa-history"></i> LOADING
                                    TRANSACTION HISTORY

                                </div>
                                <div class="card-body ">
                                    <table class="table table-striped table-responsive table-bordered mb-0" id="example">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Transaction Type</th>
                                                <th class="text-right">Amount</th>
                                                <th>From</th>
                                                <th>To</th>
                                                <th>Details</th>
                                                {{-- <th>Transaction By</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($wallet_logs as $wallet_log)
                                                <tr>
                                                    <td>{{ @date_format($wallet_log->created_at, 'F d, Y') }}</td>
                                                    <td>{{ @$wallet_log->transaction_type }}</td>
                                                    <td>{{ number_format(@$wallet_log->amount , 2, '.', ',')}}</td>
                                                    <td>{{ @$wallet_log->from->name }}</td>
                                                    <td>{{ @$wallet_log->to->name }}</td>
                                                    <td>{{ @$wallet_log->details }}</td>
                                                    {{-- <td>{{ @$wallet_log->to->name }}</td> --}}
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {{-- <nav aria-label="Page navigation example" class="float-right mt-3 ">
                                        <ul class="pagination">
                                            <li class="page-item {{ $current_page == 1 ? 'disabled ' : '' }}">
                                                <button class="page-link" onclick="search('{{ $current_page - 1 }}')" aria-label="Previous">
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
            @include('layouts.footers.auth')
        </div>
    @endsection

    @push('js')
        <script>
            const parseParams = (querystring) => {

                // parse query string
                const params = new URLSearchParams(querystring);

                const obj = {};

                // iterate over all keys
                for (const key of params.keys()) {
                    if (params.getAll(key).length > 1) {
                        obj[key] = params.getAll(key);
                    } else {
                        obj[key] = params.get(key);
                    }
                }

                return obj;
            };

            function search(page = 1) {
                // create new URL object
                let transactionType = document.getElementById('transaction-type').value;
                let username = document.getElementById('username').value;
                //    const url = new URL('{{ url()->full() }}');
                //     let link = parseParams(url.search)
                var search = location.search.substring(1);
                let link = {};
                let params = {};

                if (search) {
                    link = JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}', function(key,
                        value) {
                        return key === "" ? value : decodeURIComponent(value)
                    })
                    console.log(link);
                }
                params = {
                    page: page ? page : "1",
                    transaction_type: transactionType != '' ? transactionType : (link.transaction_type ? link
                        .transaction_type : ''),
                    username: username != '' ? username : (link.username ? link.username : ''),
                }
                let seachParams = $.param(params)
                console.log(seachParams);
                window.location.href = `{{ url()->current() }}?${seachParams}`;
            }

        </script>
    @endpush
</div>
