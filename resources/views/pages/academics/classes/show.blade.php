@extends('layouts.master')
@section('page_title', 'Class - '.$class['course_code'])
@section('content')
    @php
        use App\Helpers\Qs;
    @endphp
    <div class="row">
        <div class="col-md-3 text-center">
            <div class="card">
                <div class="card-body">
                    {{--                    <img style="width: 90%; height:90%" src="{{ 00 }}" alt="photo" class="rounded-circle">--}}
                    <br>
                    <h3 class="mt-3">{{ $class['academicPeriod']['code'] }}</h3>
                    <h3 class="mt-3">{{ $class['course_code'] }}</h3>
                    <h3 class="mt-3">{{ $class['course_name'] }}</h3>
                    <p class="mt-3">Total Students: {{$class['enrolledStudentsCount']}}</p>
                    <p class="mt-3">Instructor: {{$class['instructor']}}</p>
                </div>
            </div>
        </div>
        <div class="col-md-9">
                <div class="card card-collapsed">
                    <div class="card-header header-elements-inline">
                        <h6 class="card-title">{{ 'Class' }}</h6>
                        {!! Qs::getPanelOptions() !!}
                    </div>

                    <div class="card-body">
                        <ul class="nav nav-tabs nav-tabs-highlight">
                            <li class="nav-item"><a href="#all-Students" class="nav-link active" data-toggle="tab">All Students</a></li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="all-Students">
                                <table class="table datatable-button-html5-columns">
                                    <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Student Number</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Gender</th>
                                        <th>Percentage</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($class['students'] as $stud)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $stud['student_id'] }}</td>
                                            <td>{{ $stud['names'] }}</td>
                                            <td>{{ $stud['email'] }}</td>
                                            <td>{{ $stud['gender'] }}</td>
                                            <td>{{ $stud['paymentPercentage'] }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

        </div>

    </div>

@endsection
