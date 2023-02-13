@extends('layouts.appSuperAdmin')
@section('content')
    @include('layouts.superAdminNavbars.headers.cardsUserApproval')
    <div class="container-fluid mt--7">
        <div class="container-fluid mt--6">
            <div class="row justify-content-center">
                <div class="col">
                    <div class="card">
                        <div class="card-header bg-transparent">
                            @if ($errors->has('password'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <span class="alert-icon"><i class="fa fa-exclamation-circle"></i></span>
                                    <strong>Error!</strong> {{ $errors->first('password') }}
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
                            <div class="row m-0">
                                <div class="col-sm-12 col-md-6">
                                    <h3 class=""><i class="fa fa-list"></i> LIST OF
                                        {{ strtoupper(auth()->user()->name) }}'s
                                        PLAYERS</h3>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <form method="GET" action="" class="float-right" accept-charset="UTF-8" role="form"
                                        class="form-inline">
                                        <div class="input-group input-group-sm mb-3">
                                            <input type="text" class="form-control" placeholder="Search Player"
                                                aria-label="Search Player" id="username" name="username"
                                                aria-describedby="button-addon2">
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
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <div>
                                    <table class="table table-responsive align-items-center " id="example">
                                        <thead class="thead-light">
                                            <tr>
                                                {{-- <th scope="col" class="sort" data-sort="id">#</th> --}}
                                                <th scope="col" class="sort" data-sort="details">DETAILS</th>
                                                <th scope="col" class="sort" data-sort="date">DATE REGISTERED</th>
                                                <th scope="col">ACTIONS</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list">
                                            @foreach ($users as $user)
                                                <tr>

                                                    {{-- <td class="details">
                                                        {{ $loop->index + 1 }}
                                                    </td> --}}

                                                    <th scope="row">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <p class="m-0"><i class="fas fa-portrait"></i>
                                                                    {{ $user->username }}
                                                                </p>
                                                                <p class="m-0"><i class="fas fa-phone-square"></i>
                                                                    {{ $user->phone_number }}</p>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <p class="m-0"><i class="fas fa-address-card"></i>
                                                                    {{ $user->name }}</p>
                                                                <p class="m-0"><i class="fab fa-facebook"></i>
                                                                    {{ $user->facebook_link }}</p>
                                                            </div>
                                                        </div>
                                                    </th>
                                                    <td>
                                                        {{ date_format($user->created_at, 'F d, Y') }}
                                                    </td>
                                                    <td class="text-right">
                                                        <div class="media align-items-center">

                                                            <div class="dropdown">
                                                                <button class="btn btn-icon btn-outline-secondary text-dark"
                                                                    type="button" data-toggle="dropdown"
                                                                    aria-haspopup="true" aria-expanded="false">
                                                                    <span class="btn-inner--icon"><i
                                                                            class="fas fa-ellipsis-h"></i></i></span>
                                                                </button>
                                                                <div
                                                                    class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                                    <a class="dropdown-item" style="cursor: pointer;"
                                                                        onclick="approve({{ $user->id }},'{{ $user->name }}','Approve')"><i
                                                                            class="fa fa-check"></i> Approve</a>
                                                                    <a class="dropdown-item" style="cursor: pointer;"
                                                                        onclick="approve({{ $user->id }},'{{ $user->name }}','Disapprove')"><i
                                                                            class="fa fa-times"></i> Disapprove</a>

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
        <form action="{{ auth()->user()->user_level == 'admin' ? route('admin-user-approval-confirm') : route('user-approval-confirm') }}" id="user-accept" method="post" hidden>
            @csrf
            <input type="text" id="user-id" name="user_id">
            <input type="text" id="user-status" name="status">
        </form>
        @include('layouts.footers.auth')
        @endsection @push('js')
        <script>
            function approve(userId, name, status) {
                Swal.fire({
                    title: `${status} ${name}?`,
                    // text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: `${status}`
                }).then((result) => {
                    if (result.isConfirmed) {
                        let form = document.getElementById("user-accept");
                        document.getElementById('user-status').value = status;
                        document.getElementById('user-id').value = userId;

                        Swal.fire(
                            'Success!',
                            `${name} ${status} `,
                            'success'
                        )
                        form.submit();
                    }
                })

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
                    username: username?username:(link.username ? link.username : ''),
                }
                let seachParams = $.param(params)
                console.log(seachParams);
                window.location.href = `{{ url()->current() }}?${seachParams}`;
            }

        </script>
    @endpush
</div>
