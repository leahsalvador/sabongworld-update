@extends('layouts.appSuperAdmin')
@section('content')
    @include('layouts.superAdminNavbars.headers.cardsCurrentGame')
    <div class="container-fluid mt--7">
        <div class="container-fluid mt--6">
            <div class="row justify-content-center">
                <div class="col">
                    <div class="card">
                        <div class="card-header bg-transparent">
                            <div class="row">
                                <div class="col-sm-12 col-md-12">
                                    <div class="col-sm-12">
                                        <livewire:current-game />
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
                link = JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}', function(
                    key,
                    value) {
                    return key === "" ? value : decodeURIComponent(value)
                })
                console.log(link);
            }
            params = {
                page: page ? page : "1",
                transaction_type: transactionType != '' ? transactionType : (link.transaction_type ?
                    link
                    .transaction_type : ''),
                username: username != '' ? username : (link.username ? link.username : ''),
            }
            let seachParams = $.param(params)
            console.log(seachParams);
            window.location.href = `{{ url()->current() }}?${seachParams}`;
        }

    </script>
@endpush
