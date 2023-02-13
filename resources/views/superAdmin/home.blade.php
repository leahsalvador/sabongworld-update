@extends('layouts.appSuperAdmin')

@section('content')
    @include('layouts.superAdminNavbars.headers.cards')
    <div class="container-fluid mt--7">
        <div class="container-fluid mt--6">
            <div class="row justify-content-center">
                <div class="col">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    {{-- {{ dd($income) }} --}}
                                    <form class="form-inline" action="/superadmin/income" method="GET">
                                        <div class="form-group">
                                            <label for="example-date-input" class="form-control-label">Date From
                                                &nbsp;</label>
                                            <input class="form-control" type="date" name='date_from' id="date-from"
                                                   placeholder="Date from">
                                        </div>
                                        <div class="form-group">
                                            <label for="example-date-input" class="form-control-label">Date To
                                                &nbsp;</label>
                                            <input class="form-control" type="date" name='date_to' id="date-to"
                                                   placeholder="Date to">&nbsp;
                                        </div>
                                        <div class="form-group mr-2">&nbsp;
                                            <button class="btn btn-outline-primary"
                                                    style="z-index: 0;" o type="submit" id="button-addon2"><i
                                                    class="fa fa-search"></i> Search
                                            </button>
                                        </div>
                                    </form>
                                    <canvas id="myChart"></canvas>
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
        let labels = [];
        let dataSet = [];
        @foreach ($income as $item)
        labels.push('{{date("m/d/y",strtotime($item->date))}}')
        dataSet.push('{{$item->total}}')
        @endforeach
        console.log(labels)
        console.log(dataSet)
        // var labels = jsonfile.jsonarray.map(function(e) {
        //     return e.name;
        // });
        // var data = jsonfile.jsonarray.map(function(e) {
        //     return e.age;
        // });;

        var ctx = document.getElementById('myChart').getContext('2d');
        // const labels = [1, 2, 3, 4, 5, 6, 7];
        const data = {
            labels: labels,
            datasets: [{
                label: '{{env('APP_NAME')}} Cash Flow',
                data: dataSet,
                fill: false,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        };
        var myChart = new Chart(ctx, {
            type: 'line',
            data: data,
        });

    </script>
@endpush
