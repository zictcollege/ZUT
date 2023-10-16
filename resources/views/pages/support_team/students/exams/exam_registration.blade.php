@extends('layouts.master')
@section('page_title', 'Transcript')
@section('content')
    @php
        use App\Helpers\Qs;
    @endphp

    <div class="card">
        <div class="card-header header-elements-inline">
            {{--            {!! Qs::getPanelOptions() !!}--}}
        </div>

        <div class="card-body">


            <div class="row">
                <div class="col-md-12">
                    <!-- main header -->
                    <div class="col-md-12 app-header box">
                        <div class="row">
                            <div class="col-md-8">
                                <h3>Exam Management</h3>
{{--                                <h4>{{ $user->currentProgram->qualification }} - {{ $user->currentProgramName }}</h4>--}}
{{--                                <p>{{ $user->currentModeName }}</p>--}}
                                <p>Download Status: approved, you
                                        can download your Examination Slip</p>
                            </div>
                            <div class="col-md-4">
{{--                                <h4>Payment Percentage {{ $user->paymentPercentage }} %</h4>--}}
{{--                                <p>--}}
{{--                                    In order to qualify for exam registration, you need to make a payment equal to or--}}
{{--                                    above 70 percent of your tuition invoice which is--}}
{{--                                    K{{ ((70 - $user->paymentPercentage) / 100) * ($this->amountData[0]->Amount) }}--}}
{{--                                </p>--}}
                                <hr/>
                                <button class="btn-block" disabled type="button">Download Exam Slip
                                </button>
                                <button class="btn-block" type="button">Download Exam Slip
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="padding-top: 20px;">
                        <h4>Courses to be examined</h4>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Course Title</th>
                                <th>Course Code</th>
                                <!-- Add more columns here as needed -->
                            </tr>
                            </thead>
                            <tbody>
                            {{--                            @foreach ($user->currentRegisteredClasses as $course)--}}
                            {{--                                <tr>--}}
                            {{--                                    <td>{{ $course->course_title }}</td>--}}
                            {{--                                    <td>{{ $course->course_code }}</td>--}}
                            {{--                                    <!-- Add more columns here as needed -->--}}
                            {{--                                </tr>--}}
                            {{--                            @endforeach--}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--Class List Ends--}}

@endsection
