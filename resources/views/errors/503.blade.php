@extends('layouts.app', ['class' => 'bg-default'])

@section('content')
    @include('layouts.headers.guest')

    <div class="container mt--8 pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card bg-secondary shadow border-0">
                    <div class="card-body px-lg-5 py-lg-5">
                        <h1 class="text-danger">{{__('Sorry for inconvenience this website is under maintenance')}}</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
