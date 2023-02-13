@extends('layouts.appAgent')
@section('content')
    @include('layouts.agentNavbars.headers.cardsWithdraw')
    <div class="container-fluid mt--7">
        <div class="container-fluid mt--6">
            <div class="row justify-content-center">
                <div class="col">
                    <div class="card">
                        <div class="card-header bg-transparent">
                            @if ($errors->has('amount'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <span class="alert-icon"><i class="fa fa-exclamation-circle"></i></span>
                                    <strong>Error!</strong> {{ @$errors->first('amount') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            @if (\Session::has('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                                    <span class="alert-text"><strong>Success!</strong> {!! \Session::get('success') !!}</span>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            @if (\Session::has('failed'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <span class="alert-icon"><i class="fa fa-exclamation-circle"></i></span>
                                    <span class="alert-text"><strong>Rejected!</strong> {!! \Session::get('failed') !!}</span>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            <div class="row mb-0" >
                                <div class="col-sm-12 col-md-6">
                                    <h4><i class="fa fa-list"></i> PENDING WITHDRAWAL REQUESTS </h4>
                                </div>
                                {{--<div class="col-sm-12 col-md-6">
                                    <form method="GET" action="" class="float-right form-inline" accept-charset="UTF-8"
                                        id="search-transaction" role="form">
                                            <div class="form-group ">
                                                <select class="form-control col" name="username" id="username">
                                                    <option value="" selected>{{ @app('request')->input('username') ?app('request')->input('username') :'Select a player' }}</option>
                                                    @foreach ($players as $player)
                                                    <option value="{{@$player->username}}">{{@$player->username}}</option>
                                                  @endforeach
                                                </select>
                                              </div>
                                              <div class="input-group-append">
                                                <button class="btn btn-outline-primary" onclick="search()"
                                                    style="z-index: 0;" type="button" id="button-addon2"><i
                                                        class="fa fa-search"></i> Search</button>
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
                                            <table class="table table-responsive table-responsive align-items-center" id="example">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th scope="col" class="sort" data-sort="id">Date</th>
                                                        <th scope="col" class="sort" data-sort="details">Details</th>
                                                        <th scope="col" class="sort" data-sort="points">Username</th>
                                                        <th scope="col" class="sort" data-sort="date">Amount</th>
                                                        <th scope="col">ACTIONS</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list">
                                                    @foreach ($users as $user)
                                                        <tr>
                                                            <td class="details">
                                                                {{ @date_format($user->created_at, 'F d, Y') }}
                                                            </td>
                                                            <th scope="row">

                                                                    <div class="row">
                                                                        <div class="col-sm-6">
                                                                            {{ @$user->details }}
                                                                        </div>
                                                                </div>
                                                            </th>
                                                            <td class="details">
                                                                {{ @$user->from->username }}
                                                            </td>
                                                            <td>
                                                                {{ number_format(@$user->amount , 2, '.', ',') }}
                                                            </td>
                                                            <td class="text-right">
                                                                {{-- <div class="media align-items-center">
                                                                    <button class="btn btn-outline-success btn-sm" onclick="activateUser({{ @$user->id }},'{{ @$user->name }}','activated')"> <i class="fa fa-check"></i> Activate </button>
                                                                </div> --}}
                                                                <div class="media align-items-center">
                                                                    <div class="dropdown">
                                                                        <button class="btn btn-icon btn-outline-secondary text-dark"
                                                                            type="button" data-toggle="dropdown" aria-haspopup="true"
                                                                            aria-expanded="false">
                                                                            <span class="btn-inner--icon"><i
                                                                                    class="fas fa-ellipsis-h"></i></i></span>
                                                                        </button>
                                                                        <div
                                                                            class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                                            <a class="dropdown-item" style="cursor: pointer;"
                                                                                onclick="confirmAction({{ @$user->id }},'{{ @$user->from->username }}','Accept',{{ @$user->amount }})"><i
                                                                                    class="fa fa-check"></i> Accept</a>
                                                                            @if (auth()->user()->user_level == 'master-agent')
                                                                            <a class="dropdown-item" style="cursor: pointer;"
                                                                            onclick="confirmAction({{ @$user->id }},'{{ @$user->from->username }}','Reject',{{ @$user->amount }})"><i
                                                                                class="fa fa-times"></i> Reject</a>
                                                                            @endif

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
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
        @include('layouts.footers.auth')
        @endsection @push('js')
        <form action="{{ auth()->user()->user_level == 'admin' ? route('admin-withdraw-request-confirm') : route('withdraw-request-confirm') }}" id="withdraw-request" method="post" hidden>
            @csrf
            <input type="number" id="transaction-id" name="transaction_id">
            <input type="text" id="type" name="type">
            <input type="number" id="amount" name="amount">
        </form>
        <script>
                        function confirmAction(transactionId, name, type,amount) {
                Swal.fire({
                    title: `${type} ${name} request?`,
                    // text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: `${type}`
                }).then((result) => {
                    if (result.isConfirmed) {
                        let form = document.getElementById("withdraw-request");
                        document.getElementById('type').value = type;
                        document.getElementById('amount').value = amount;
                        document.getElementById('transaction-id').value = transactionId;

                        Swal.fire(
                            'Processing!',
                            `Processing request`,
                            'info'
                        )
                        form.submit();
                    }
                })

            }
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
                    username:username?username: (link.username ? link.username : ''),
                }
                let seachParams = $.param(params)
                console.log(seachParams);
                window.location.href = `{{ url()->current() }}?${seachParams}`;
            }

        </script>
    @endpush
</div>
