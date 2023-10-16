@extends('layouts.master')
@section('page_title', 'Finance')
@section('content')
    @php
        use App\Helpers\Qs;
    @endphp

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Accounting Information</h6>
            {!! Qs::getPanelOptions() !!}
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#invoices" class="nav-link active" data-toggle="tab">Invoices</a></li>
                <li class="nav-item"><a href="#receipts" class="nav-link" data-toggle="tab">Receipts</a></li>
                <li class="nav-item"><a href="#statement" class="nav-link" data-toggle="tab">Statement</a></li>
            </ul>

            <div class="tab-content">
                {{--Basic Info--}}

                <div class="tab-pane fade show active" id="invoices">
                    <table class="table datatable-button-html5-columns">
                        <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Total</th>
                            <th>Operation</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($accounting['invoices'] as $inv)
                            <tr>
                                <td>{{ $inv['id'] }}</td>
                                <td>{{ $inv['date'] }}</td>
                                <td> Invoice </td>
                                <td>{{ $inv['total'] }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade show" id="receipts">
                    <table class="table datatable-button-html5-columns">
                        <thead>
                        <tr>
                            <th>Receipt #</th>
                            <th>Payment Method</th>
                            <th>Collected By</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Operation</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($accounting['receipts'] as $recei)
                            <tr>
                                <td>{{ $recei['id'] }}</td>
                                <td>{{ $recei['payment_method'] }}</td>
                                <td>{{ $recei['collectedBy'] }}</td>
                                <td>{{ $recei['date'] }}</td>
                                <td>ZMW {{ $recei['ammount_paid'] }}</td>
                                <td></td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade show" id="statement">
                    <table class="table datatable-button-html5-columns">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reference #</th>
                            <th>Description</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Balance</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($accounting['statement'] as $stmt)
                            <tr>
                                <td>{{ $stmt['date'] }}</td>
                                <td>{{ $stmt['reference'] }}</td>
                                <th>{{ $stmt['description'] }}</th>
                                <td>{{ $stmt['debit'] }}</td>
                                <td>{{ $stmt['credit'] }}</td>
                                <td>ZMW {{ $stmt['balance'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{--Class List Ends--}}

@endsection
