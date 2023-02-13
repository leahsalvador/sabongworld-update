@extends('layouts.appAgent')
@section('content')
    @include('layouts.agentNavbars.headers.cardsComissionEdit')
    {{-- {{dd(url()->full())}} --}}
    <div class="container-fluid mt--7">
        <div class="container-fluid mt--6">
            <div class="row justify-content-center">
                <div class="col">
                    <div class="card">
                        <div class="card-header bg-transparent">
                            <div class="row">
                                <div class="col-xl-12 col-lg-12">
                                    <div class="card card-stats">
                                        <div class="card-header"><i
                                                class="fa fa-money-bill"></i> {{__('AGENT COMMISSION EDITTING')}}
                                            <div class="card-header-actions float-right">
                                                <div role="group" class="btn-group">
                                                    <div role="group" class="btn-group">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-striped table-responsive table-bordered mb-0"
                                                   id="example">
                                                <thead>
                                                <tr>
                                                    <th>User Type</th>
                                                    <th>Username</th>
                                                    <th>Email</th>
                                                    <th>Commission %</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($agents as $key => $agent)
                                                    <tr>
                                                        <td>{{ $dispay_name[$agent->user_level] }}</td>
                                                        <td>{{ $agent->username }}</td>
                                                        <td>{{ $agent->email }}</td>
                                                        <td>{{ $agent->commission_percent }}%</td>
                                                        <td>{{ $agent->status }}</td>
                                                        <td><a id="edit-commission" href="#"
                                                               data-user-id="{{ $agent->id }}"
                                                               data-user-type="{{$agent->user_level}}"
                                                               data-commission="{{$agent->commission_percent}}"
                                                               data-max-commission="{{$max_commission}}"
                                                            >Edit</a></td>
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
            </div>
            @include('layouts.footers.auth')
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function () {
            let updateUrl = '{{$updateUrl}}';
            let maxCom = {{$max_commission}};
            let minCom = {{$min_commission}};

            $(document).on('click', '#edit-commission', function () {
                let userId = $(this).data('user-id');
                let userType = $(this).data('user-type');
                console.log(userId + '::' + userType);
                Swal.fire({
                    title: 'Enter Agent Commission',
                    input: 'number',
                    inputValue: $(this).data('commission'),
                    inputAttributes: {
                        min: minCom,
                        max: maxCom,
                        step: .10,
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    showLoaderOnConfirm: true,
                    inputValidator: (value) => {
                        if (!value) {
                            return '<label class="text-red">You need to write something!</label>';
                        } else if (value > maxCom || value < minCom) {
                            return '<label class="text-red">Commission must be between <strong>' + minCom + ' to ' + maxCom + '</strong></label>';
                        } else if (value < 0) {
                            return '<label class="text-red">Commission must be an positive integer.</label>';
                        }
                    },
                    preConfirm: (commission) => {

                        return fetch(`{{$updateUrl}}/${commission}/${userId}/`)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(response.statusText)
                                }
                                return response.json()
                            })
                            .catch(error => {
                                Swal.showValidationMessage(
                                    `Request failed: ${error}`
                                )
                            })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    console.log(result);
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: "Commission save successfully.",
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });
                    }
                });
            });
        });
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
            let dateTo = document.getElementById('date-to').value;
            let dateFrom = document.getElementById('date-from').value;
            //    const url = new URL('{{ url()->full() }}');
            //     let link = parseParams(url.search)
            var search = location.search.substring(1);
            let link = {};
            let params = {};
            if (search) {
                link = JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}', function (key, value) {
                    return key === "" ? value : decodeURIComponent(value)
                })
                console.log(link);
            }
            params = {
                page: page ? page : "1",
                date_to: dateTo != '' ? dateTo : (link.date_to ? link.date_to : ''),
                date_from: dateFrom != '' ? dateFrom : (link.date_from ? link.date_from : ''),
            }

            let seachParams = $.param(params)
            console.log(seachParams);
            window.location.href = `{{ url()->current() }}?${seachParams}`;
        }

    </script>
@endpush
