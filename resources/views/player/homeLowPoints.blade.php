@extends('layouts.appPlayer')
@section('content')
    @include('layouts.playerNavbars.headers.cards')
        <div class="card card-stats bg-default ">
            <div class="card-body bg-default ">
                <div class="row">
                    <div class="col text-center">
                        <span class="h2 font-weight-bold text-white mb-0"><div class="icon icon-shape bg-red text-white rounded-circle shadow">
                            <i class="fa fa-times"></i>
                        </div></span>
                        <br>
                        <br>
                        <h5 class="card-title text-uppercase text-white mb-0">Cannot enter the game Points less than 20 </h5>
                        <span class="h2 font-weight-bold text-white mb-0">Current Points: <strong class="text-red"> {{ number_format(auth()->user()->wallet->points, 2, '.', ',') }}</strong></span>

                    </div>
                </div>
            </div>
        </div>

@endsection

@push('js')


@endpush
