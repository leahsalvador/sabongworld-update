@extends('layouts.appSuperAdmin')
@section('content')
    @include('layouts.superAdminNavbars.headers.cardsDeactivatedUser')
    <div class="container-fluid mt--7">
        <div class="container-fluid mt--6">
            <div class="row justify-content-center">
                <div class="col">
                    <div class="card">
                        <div class="card-header bg-transparent">
                            @if ($errors->has('password'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <span class="alert-icon"><i class="fa fa-exclamation-circle"></i></span>
                                    <strong>Error!</strong> {{ @$errors->first('password') }}
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
                            <div class="row mb-0">
                                <div class="col-sm-12 col-md-6">
                                    <h4><i class="fa fa-list"></i> LIST OF
                                        {{ @strtoupper(auth()->user()->username) }}'s
                                        PLAYERS</h4>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <form method="GET" action="" class="float-right" accept-charset="UTF-8"
                                        id="search-transaction" role="form" class="form-inline">
                                        <div class="input-group input-group-sm mb-3">
                                            <input type="text" class="form-control" placeholder="Search Player"
                                                aria-label="Search Player" id="username" name="username"
                                                value="{{ @app('request')->input('username') ? app('request')->input('username') : '' }}"
                                                aria-describedby="button-addon2">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-primary" onclick="search()"
                                                    style="z-index: 0;" type="button" id="button-addon2"><i
                                                        class="fa fa-search"></i> Search</button>
                                            </div>
                                            <div class="input-group-append">
                                                <a class="btn btn-outline-danger" href="{{ url('superadmin/clear-player-deactivated') }}"
                                                    style="z-index: 0;" type="button" id="button-addon2"><i
                                                        class="fa fa-ban"></i> Clear</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <div>
                                            <table class="table align-items-center table-responsive" id="example">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th scope="col" class="sort" data-sort="id">#</th>
                                                        <th scope="col" class="sort" data-sort="details">DETAILS</th>
                                                        <th scope="col" class="sort" data-sort="points">CURRENT POINTS</th>
                                                        <th scope="col" class="sort" data-sort="date">DATE REGISTERED</th>
                                                        <th scope="col">ACTIONS</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list">
                                                    @foreach ($users as $user)
                                                        <tr>
                                                            <td class="details">
                                                                {{ $loop->index + 1 }}
                                                            </td>
                                                            <th scope="row">

                                                                <div class="row">
                                                                    <div class="col-sm-6">
                                                                        <p class="m-0"><i class="fas fa-portrait"></i>
                                                                            {{ @$user->username }}
                                                                        </p>
                                                                        <p class="m-0"><i class="fas fa-phone-square"></i>
                                                                            {{ @$user->phone_number }}</p>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <p class="m-0"><i class="fas fa-address-card"></i>
                                                                            {{ @$user->name }}</p>
                                                                        <p class="m-0"><i class="fab fa-facebook"></i>
                                                                            {{ @$user->facebook_link }}</p>
                                                                    </div>

                                                                </div>
                                                            </th>
                                                            <td class="details">
                                                                {{ number_format(@$user->wallet->points, 2, '.', ',') }}
                                                            </td>
                                                            <td>
                                                                {{ @date_format($user->created_at, 'F d, Y') }}
                                                            </td>
                                                            <td class="text-right">
                                                                <div class="media align-items-center">
                                                                    <button class="btn btn-outline-success btn-sm"
                                                                        onclick="activateUser({{ @$user->id }},'{{ @$user->name }}','activated')">
                                                                        <i class="fa fa-check"></i> Activate </button>
                                                                </div>
                                                                <br>
                                                                <div class="media align-items-center">
                                                                    <button class="btn btn-outline-primary btn-sm"
                                                                            onclick="editUser({{ @$user->id }}, '{{ @$user->name }}', '{{ @$user->phone_number }}')">
                                                                        <i class="fa fa-check"></i> Edit User </button>
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
                                            <button class="page-link" onclick="search('{{ $current_page - 1 }}')"
                                                aria-label="Previous">
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
    <div class="modal fade" id="activate-modal" tabindex="-1" role="dialog" aria-labelledby="activate-modal"
        aria-hidden="true">
        <div class="modal-dialog modal-danger modal-dialog-centered" role="document">
            <div class="modal-content bg-gradient-danger">


                <div class="modal-header">
                    <h6 class="modal-title" id="modal-title-notification">
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div class="py-3 text-center">
                        <span style="font-size: 5rem;">
                            <i class="fas fa-user-check"></i>
                        </span>
                        <h4 class="heading mt-4">Activate User</h4>
                        <p>Please Input your password
                            for confirmation</p>
                        <form id="activate-form"
                            action="{{ route('superadmin-players-change-status')}}"
                            method="post">
                            @csrf
                            <div class="form-group col-sm-12">
                                <div class="input-group">
                                    <input id="password" placeholder="Enter password" required="required" name="password"
                                        type="password" class="form-control" />
                                </div>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback text-white" style="display: block;" role="alert">
                                        <strong>{{ @$errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-white ml-auto" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-white"><i class="fa fa-save"></i>
                        Submit</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit-user-modal" tabindex="-1" role="dialog" aria-labelledby="edit-modal"
         aria-hidden="true">
        <div class="modal-dialog modal-danger modal-dialog-centered" role="document">
            <div class="modal-content bg-gradient-danger">

                <div class="modal-header">
                    <h6 class="modal-title" id="modal-title-notification">
                    </h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="py-3 text-center">
                            <span style="font-size: 5rem;">
                                <i class="fas fa-user-edit"></i>
                            </span>
                        <h4 class="heading mt-4">Edit User</h4>

                        <form  action="{{ url('superadmin/edit-user-info') }}" method="post">

                            @csrf
                            <div class="form-group col-sm-12">
                                <div class="input-group">
                                    <input id="name" placeholder="Enter Name" required="required"
                                           name="name" type="test" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <div class="input-group">
                                    <input id="phone_number" placeholder="Enter Phone Number" required="required"
                                           name="phone_number" type="text" class="form-control" />
                                </div>
                            </div>
                            <input hidden id="edit_user_id" name="user_id" type="text" class="form-control" />
                        {{--<p>Please Input your password--}}
                        {{--for confirmation</p>--}}
                        {{--<div class="form-group col-sm-12">--}}
                        {{--<div class="input-group">--}}
                        {{--<input id="password" placeholder="Enter password" required="required"--}}
                        {{--name="password" type="password" class="form-control" />--}}
                        {{--</div>--}}
                        {{--@if ($errors->has('password'))--}}
                        {{--<span class="invalid-feedback text-white" style="display: block;" role="alert">--}}
                        {{--<strong>{{ @$errors->first('password') }}</strong>--}}
                        {{--</span>--}}
                        {{--@endif--}}
                        {{--</div>--}}

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-white ml-auto" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-white"><i class="fa fa-save"></i>
                        Submit</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function  editUser(userId, name, phone_number) {
            document.getElementById('edit_user_id').value = userId;
            document.getElementById('name').value = name;
            document.getElementById('phone_number').value = phone_number;
            $('#edit-user-modal').modal('show');
        }

        function activateUser(userId, name, status) {
            $('#activate-modal').modal('show');
            let form = document.getElementById("activate-form");
            console.log(form.elements["user_id"]);

            if (!form["status"]) {
                let el = document.createElement("input");
                el.name = "status";
                el.id = "status";
                el.value = status;
                // el.type = "text";
                el.type = "hidden";

                console.log(el);
                form.appendChild(el);
            } else {
                document.getElementById('status').value = status;
            }
            if (!form["user_id"]) {
                let el = document.createElement("input");
                el.name = "user_id";
                el.id = "user-id";
                el.value = userId;
                // el.type = "text";
                el.type = "hidden";

                console.log(el);
                form.appendChild(el);
            } else {
                document.getElementById('user-id').value = userId;
            }
        }
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
</div>
