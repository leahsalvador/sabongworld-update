@extends('layouts.appAgent')
@section('content')
    @include('layouts.agentNavbars.headers.cardsAgentList')
    <div class="container-fluid mt--7">
        <div class="container-fluid mt--6">
            <div class="row justify-content-center">
                <div class="col">
                    <div class="card">
                        <div class="card-header bg-transparent">
                            @if ($errors->has('password'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <span class="alert-icon"><i class="fa fa-exclamation-circle"></i></span>
                                    <strong>{{__('Error!')}}</strong> {{ $errors->first('password') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            @if (\Session::has('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                                    <span class="alert-text"><strong>{{__('Success!')}}</strong> {!! \Session::get('success') !!}</span>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            <div class="row m-0">
                                <div class="col-sm-6 col-md-6">
                                    <h3 class=""><i class="fa fa-list"></i> Transaction Histroy </h3>
                                </div>
                                {{--<div class="col-sm-12 col-md-6">
                                    <form method="GET" action="" class="float-right" accept-charset="UTF-8" role="form"
                                        class="form-inline">
                                        <div class="input-group input-group-sm mb-3">
                                            <input type="text" class="form-control" placeholder="Search Player" value="{{ @app('request')->input('username') ?app('request')->input('username') :'' }}"
                                                aria-label="Search Player" id="username" name="username"
                                                aria-describedby="button-addon2">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-primary" onclick="search()"
                                                    style="z-index: 0;" type="button" id="button-addon2"><i
                                                        class="fa fa-search"></i> {{__('Search')}}</button>
                                            </div>
                                            <div class="input-group-append">
                                                <a class="btn btn-outline-danger" href="{{ url('admin/clear-agent-list') }}"
                                                    style="z-index: 0;" type="button" id="button-addon2"><i
                                                        class="fa fa-ban"></i> {{__('Clear')}}</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>--}}
                            </div>
                            <div class="table-responsive">
                                <div>
                                    <table class="table table-responsive align-items-center " id="example">
                                        <thead class="thead-light">
                                            <tr>

                                                <th scope="col" class="sort" data-sort="id">#</th>
                                                <th scope="col" class="sort" data-sort="id">Datetime</th>
                                                <th scope="col" class="sort" data-sort="id">Transaction Type</th>
                                                <th scope="col" class="sort" data-sort="details">Name</th>
                                                <th scope="col" class="sort" data-sort="details">User Type</th>
                                                <th scope="col" class="sort" data-sort="points">commission rate</th>
                                                <th scope="col" class="sort" data-sort="points">commission Earned</th>
                                                <th scope="col" class="sort" data-sort="points">Cash in amount</th>
                                                <th scope="col" class="sort" data-sort="date">Cashout amount</th>
                                                <th scope="col">Current Points</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list">

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
        @include('layouts.footers.auth')
        @endsection @push('js')
    @endpush
    </div>
