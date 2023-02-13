@extends('layouts.appAgent')
@section('content')
    @include('layouts.agentNavbars.headers.cardsCurrentGame')
    <div class="container-fluid mt--7">
        <div class="container-fluid mt--6">
            <div class="row justify-content-center">
                <div class="col">
                    <div class="card">
                        <div class="card-header bg-transparent">
                            <div class="row">
                                <div class="col-sm-12 col-md-12">
                                    <div class="col-sm-12">
                                        <livewire:current-game/>
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
        $(document).ready(function () {
            var gameTable = $('#games').DataTable({
                dom: 'Bfrtip',
                pageLength: 20,
                order: [],
                buttons: [
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5'
                ]
            });

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

            $(document).on('click', '#reset-game', function () {
                Swal.fire({
                    title: 'Are you sure want to delete <br> round & betting history ?',
                    showCancelButton: true,
                    confirmButtonText: 'Confirm',
                    showLoaderOnConfirm: true,
                    preConfirm: (commission) => {
                        return fetch(`{{route('admin.game.reset')}}`)
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
                            title: "Game Reset Successfully.",
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
            let transactionType = document.getElementById('transaction-type').value;
            let username = document.getElementById('username').value;
            //    const url = new URL('{{ url()->full() }}');
            //     let link = parseParams(url.search)
            var search = location.search.substring(1);
            let link = {};
            let params = {};

            if (search) {
                link = JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}', function (
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
