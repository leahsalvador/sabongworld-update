@extends('layouts.appAgent')
@section('content')
    @include('layouts.agentNavbars.headers.cardsComissionLog')
    <div class="container-fluid mt--7">
        <div class="container-fluid mt--6">
            <div class="row justify-content-center">
                <div class="col">
                    <div class="card">
                        <div class="card-header bg-transparent">
                            <div class="row">
                                <div class="col-sm-12 col-md-12"></div>
                            </div>
                            <div class="card card-stats">
                                <div class="card-header">
                                    <i class="fa fa-history"></i>
                                    COMISSION TRANSACTION HISTORY
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table
                                            class="table align-items-center table-bordered table-condensed table-striped"
                                            id="example">
                                            <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Transaction Type</th>
                                                <th class="text-right">Amount</th>
                                                <th>From</th>
                                                <th>To</th>
                                                <th>Details</th>
                                                <th>Current Commission</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($comission_logs as $comission_log)
                                                <tr>
                                                    <td>{{date_format(date_create($comission_log->created_at),"F d,Y")}}
                                                        <br>{{date_format(date_create($comission_log->created_at),"h:i A")}}
                                                    </td>
                                                    <td>{{ @$comission_log->transaction_type }}</td>
                                                    <td>{{ number_format(@$comission_log->amount, 2, '.', ',') }}</td>
                                                    <td>{{ @$comission_log->from->name }}</td>
                                                    <td>{{ @$comission_log->to->name }}</td>
                                                    <td>{{ @$comission_log->details ? $comission_log->details : 'none' }}</td>
                                                    <td>{{$comission_log->comission}}</td>
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
                    let member = document.getElementById('member').value;
                    //    const url = new URL('{{ url()->full() }}');
                    //     let link = parseParams(url.search)
                    var search = location.search.substring(1);
                    let link = {};
                    if (search) {
                        link = JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}', function (key,
                                                                                                                    value) {
                            return key === "" ? value : decodeURIComponent(value)
                        })
                        console.log(link);
                    }
                    let params = {
                        page: page ? page : "1",
                        transaction_type: transactionType ? transactionType : (link.transaction_type ? link.transaction_type : ''),
                        member: member ? member : (link.member ? link.member : ''),
                    }
                    let seachParams = $.param(params)
                    console.log(seachParams);
                    window.location.href = `{{ url()->current() }}?${seachParams}`;
                }

            </script>
        @endpush
    </div>
