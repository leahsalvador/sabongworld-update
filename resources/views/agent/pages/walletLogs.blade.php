@extends('layouts.appAgent')
@section('content')
    @include('layouts.agentNavbars.headers.cardsWalletLogs')
    <div class="container-fluid mt--7">
        <div class="container-fluid mt--6">
            <div class="row justify-content-center">
                <div class="col">
                    <div class="card">
                        <div class="card-header bg-transparent">
                            <div class="row">
                                <div class="col-sm-12 col-md-12">

                                </div>
                            </div>
                            <div class="card card-stats">
                                <div class="card-header"><i class="fa fa-history"></i> LOADING
                                    TRANSACTION HISTORY

                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table align-items-center table-bordered table-condensed table-striped"
                                               style="width:99%;" id="example">
                                            <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Transaction Type</th>
                                                <th class="text-right">Amount</th>
                                                <th>From</th>
                                                <th>To</th>
                                                <th>Details</th>
                                                <th>Current Points</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($wallet_logs as $log)
                                                <tr>
                                                    <td>{{date_format(date_create($log->created_at),"F d,Y")}}<br>{{date_format(date_create($log->created_at),"h:i A")}}       </td>
                                                    <td>{{ @$log->transaction_type }}</td>
                                                    <td>{{ number_format(@$log->amount , 2, '.', ',')}}</td>
                                                    <td>{{ @$log->from->name }} <br>({{(@$log->from->username)}})</td>
                                                    <td>{{ @$log->to->name }} <br> ({{(@$log->to->username)}})</td>
                                                    <td>{{ @$log->details }}</td>
                                                    <td>{{ @$log->points }}</td>
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
                        link = JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}', function (key,
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
