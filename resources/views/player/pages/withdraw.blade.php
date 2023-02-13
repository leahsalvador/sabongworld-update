@extends('layouts.appPlayer')
@section('content')
    @include('layouts.playerNavbars.headers.cards')

    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-5 mt-3">
                <div class="card bg-default border-light">
                    <div class="card-header bg-default">
                        <label for="exampleFormControlSelect1 bg-default" class="text-white">Transaction
                            Type</label>
                        <h5 class="text-primary float-right">WITHDRAWAL</h5>
                    </div>
                    <form method="POST" id="withdraw-form" action="{{route('player-withdraw')}}">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="exampleFormControlInput1" class="text-white">Amount</label>
                                <input class="form-control text-white bg-default" value="{{ old('amount') ?? ''}}" type="number" min="100" name="amount"
                                    id="amount" required placeholder="Enter Amount">
                                    @if ($errors->has('amount'))
                                    <span class="invalid-feedback red-text" style="display: block;"
                                        role="alert">
                                        <strong>{{ $errors->first('amount') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlTextarea1" class="text-white">Details</label>
                                <textarea class="form-control text-white bg-default" name="details" id="details"
                                    placeholder='Enter Details &#10;Example: &#10;Bank Account Details, &#10;Gcash Number'
                                    rows="6" required>{{ old('details') ?? ''}}</textarea>
                                    @if ($errors->has('details'))
                                    <span class="invalid-feedback red-text" style="display: block;"
                                        role="alert">
                                        <strong>{{ $errors->first('details') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer bg-default float-right">
                                <button type="button" id="withdraw-button" class="btn btn-outline-success text-white"> <i class="fa fa-save"></i> Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-sm-12 col-md-7 mt-3">
                <div class="card bg-default border-light">
                    <div class="card-header bg-default">
                        <label for="exampleFormControlSelect1 bg-default" class="mb-1 text-white">Request History</label>
                        <label for="exampleFormControlInput1" class="float-right text-white">Current Points :
                            <strong style="color: #ffdc11">
                                {{ number_format(auth()->user()->wallet->points, 2, '.', ',') }}</strong></label>
                    </div>
                    <div class="card-body">
                        <div>
                            <table class="table table-responsive ml-3">
                                <thead>
                                    <tr>
                                        <th class="text-white">Date</th>
                                        <th class="text-white">Amount</th>
                                        <th class="text-white">Details</th>
                                        <th class="text-white">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($withdraw_logs as $withdraw_log)
                                        <tr>

                                            <td class="text-white">{{ @date_format($withdraw_log->created_at, 'F d, Y') }}
                                            </td>
                                            <td class="text-white">{{ number_format(@$withdraw_log->amount, 2, '.', ',') }}</td>
                                            <td class="text-white">{{ @$withdraw_log->details }}</td>

                                            <td class="text-white">
                                                @switch(@$withdraw_log->transaction_status )
                                                    @case('pending')
                                                    <span class="badge badge-dot mr-4">
                                                        <i class="bg-warning"></i>
                                                        <span class="status">Pending</span>
                                                    </span>
                                                    @break
                                                    @case('success')
                                                    <span class="badge badge-dot mr-4">
                                                        <i class="bg-success"></i>
                                                        <span class="status">Success</span>
                                                    </span>
                                                    @break
                                                    @default
                                                    <span class="badge badge-dot mr-4">
                                                        <i class="bg-danger"></i>
                                                        <span class="status">Cancelled</span>
                                                    </span>
                                                @endswitch
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <tfoot>
                                <nav aria-label="Page navigation example" class="float-right mt-3 ">
                                    <ul class="pagination">
                                        <li class="page-item  {{ $current_page == 1 ? 'disabled ' : '' }}">
                                            <a class="page-link bg-default text-white"
                                                href="{{ url()->current() . '?page=' . ($current_page - 1) }}"
                                                 aria-label="Previous">
                                                <i class="fas fa-angle-left"></i>
                                                <span class="sr-only">Previous</span>
                                            </a>
                                        </li>
                                        @for ($i = 1; $i <= $total_page; $i++)
                                            <li class="page-item  {{ $current_page == $i ? 'active' : '' }}">
                                                <a class="page-link bg-default text-white" 
                                                    href="{{ url()->current() . '?page=' . $i }}">
                                                    {{ $i }}
                                                </a>
                                        @endfor
                                        <li class="page-item  {{ $current_page == $total_page ? 'disabled' : '' }}">
                                            <a class="page-link bg-default text-white" class="page-link"
                                               href="{{ url()->current() . '?page=' . ($current_page + 1) }}"
                                                aria-label="Next">
                                                <i class="fas fa-angle-right"></i>
                                                <span class="sr-only">Next</span>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </tfoot>
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

document.getElementById("withdraw-button").addEventListener("click", (e) => {
        let amount = document.getElementById('amount').value;
        let form = document.getElementById('withdraw-form');
        form.reportValidity();
        Swal.fire({
            title: 'Are you sure?',
            text: `You want to withdraw ${amount}?`,
            showCancelButton: true,
            confirmButtonColor: '#343a40',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, place bet!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("withdraw-form").submit();
                Swal.fire(
                    'Processing!',
                    'Your Request has been sent.',
                    'info'
                );
            }
        })
    });
</script>
@endpush
