@if (!empty($results))
    @php
        $firstAcademicData = reset($results);
        $academicData = $firstAcademicData['academic'];
    @endphp

    {{--    <h2>Academic Period: {{ $academicData }}</h2>--}}
    {{--    <h3>Program: {{ $firstAcademicData['program_name'] }} ({{ $firstAcademicData['program_code'] }})</h3>--}}
    {{--    <h4>Academic Period Name: {{ $firstAcademicData['academicperiodname'] }}</h4>--}}
@endif
@extends('layouts.master')
@section('page_title', Auth::user()->first_name .'s Results')
@section('content')
    @php
        use App\Helpers\Qs;
    @endphp

    <div class="card overflow-scroll">
        <div class="card-header header-elements-inline">
            {{--            {!! Qs::getPanelOptions() !!}--}}
        </div>

        <div class="card-body">
            <div class="row justify-content-end">
                <div class="col-md-12">
                    <p>
                        This transcript may not include all courses required for your program completion.
                        Please verify with the Academics Office.
                    </p>
                </div>
            </div>
            <hr/>
        </div>

        <div class="row ">
            <div class="container">
                <div class="row justify-content-end">
                    <div class="col-md-12 p-3">
                        <h3>Program: {{ $firstAcademicData['program_name'] }} ({{ $firstAcademicData['program_code'] }}
                            )</h3>
                        <p>{{Auth::user()->first_name.' '.Auth::user()->last_name }}  </p>
                        @foreach ($results as $academicData)
                            @foreach ($academicData['students'] as $student)
                                <table class="table table-hover table-striped-columns mb-3">
                                    <h5 class="p-2"><strong>{{ $academicData['academicperiodname'] }}</strong></h5>
                                    <h5 class="p-2"><strong>{{ Auth::user()->student_id }}</strong></h5>
                                    <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>CA</th>
                                        <th>Out of</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($student['courses'] as $course)
                                        <tr>outOf
                                            <th>{{ $loop->iteration }}</th>
                                            <td>{{ $course['code'] }}</td>
                                            <td>{{ $course['title'] }}</td>
                                            <td>@foreach($course['assessments'] as $assess)
                                                    @if(!empty($assess['assessment_name']) && $assess['assessment_name']=='Exam')
                                                        {{ ($course['CA'] - $assess['total']) }}
                                                        @break
                                                    @else
                                                        {{ $course['CA']  }}
                                                        @break
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>{{ $course['outOf'] }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                </table>
                                <hr>
                            @endforeach

                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

{{--@endforeach--}}
