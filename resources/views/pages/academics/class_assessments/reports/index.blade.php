@extends('layouts.master')
@section('page_title', 'Publishing Results for '.$period->code)
@section('content')
    @php
        use App\Helpers\Qs;
    @endphp
    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Results Reports</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="container">

                <div class="row">
                    <div class="col-6">
                        <div style="text-align: center;">
                            <canvas id="myChart" style="max-width: 500px;"></canvas>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="text-align: center;">
                            <canvas id="analysisCount" style="max-width: 500px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Results Reports</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="container">
                <div class="row">
                    <div class="col-6">
                        <div style="text-align: center;">
                            <canvas id="BestBasedOnClass" style="max-width: 500px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
