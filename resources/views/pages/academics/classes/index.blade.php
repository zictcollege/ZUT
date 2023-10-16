@extends('layouts.master')
@section('page_title', 'Academic - '.$class['program']['name'])
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
                    <h3 class="mt-3">{{ $class['program']['fullname'] }}</h3>
                    <h3 class="mt-3">{{ $class['program']['code'] }}</h3>
                    <p class="mt-3">Total Students: {{$class['studentsCount']}}</p>
                </div>
            </div>
        </div>
        <div class="col-md-9">
@foreach($class['levels'] as $level)
            <div class="card card-collapsed">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">{{ $level['name'] }}</h6>
                    {!! Qs::getPanelOptions() !!}
                </div>

                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item"><a href="#all-Students{!! str_replace(' ', '', $level['name']) !!}-1" class="nav-link active" data-toggle="tab">All Students</a></li>
                        <li class="nav-item"><a href="#paid-Students{!! str_replace(' ', '', $level['name']) !!}-2" class="nav-link" data-toggle="tab">Paid Students</a></li>
                        <li class="nav-item"><a href="#unpaid-Students{!! str_replace(' ', '', $level['name']) !!}-3" class="nav-link" data-toggle="tab">Not Paid Students</a></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="all-Students{!! str_replace(' ', '', $level['name']) !!}-1">
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
                                @foreach($level['students'] as $stud)
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

                        <div class="tab-pane fade show" id="paid-Students{!! str_replace(' ', '', $level['name']) !!}-2">
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
                                @foreach($level['studentsPaid'] as $studp)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $studp['student_id'] }}</td>
                                        <td>{{ $studp['names'] }}</td>
                                        <td>{{ $studp['email'] }}</td>
                                        <td>{{ $studp['gender'] }}</td>
                                        <td>{{ $studp['paymentPercentage'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane fade show" id="unpaid-Students{!! str_replace(' ', '', $level['name']) !!}-3">
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
                                @foreach($level['studentsUnPaid'] as $studup)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $studup['student_id'] }}</td>
                                        <td>{{ $studup['names'] }}</td>
                                        <td>{{ $studup['email'] }}</td>
                                        <td>{{ $studup['gender'] }}</td>
                                        <td>{{ $studup['paymentPercentage'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    @endforeach
            <div class="card card-collapsed">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Classes</h6>
                    {!! Qs::getPanelOptions() !!}
                </div>

                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item"><a href="#all-classesList" class="nav-link active" data-toggle="tab">Available Classes</a></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="all-classesList">
                            <table class="table datatable-button-html5-columns table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Enrolled Students</th>
                                    <th>Instructor</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
{{--                                @foreach($ap['classes'] as $p)--}}
{{--                                    <tr>--}}
{{--                                        <td>{{ $loop->iteration }}</td>--}}
{{--                                        <td>{{ $p['course_code'] }}</td>--}}
{{--                                        <td>{{ $p['course_name'] }}</td>--}}
{{--                                        <td>{{ $p['enrolledStudentsCount'] }}</td>--}}
{{--                                        <td>{{ $p['instructor'] }}</td>--}}
{{--                                        <td class="text-center">--}}
{{--                                            <div class="list-icons">--}}
{{--                                                <div class="dropdown">--}}
{{--                                                    <a href="#" class="list-icons-item" data-toggle="dropdown">--}}
{{--                                                        <i class="icon-menu9"></i>--}}
{{--                                                    </a>--}}

{{--                                                    <div class="dropdown-menu dropdown-menu-left">--}}
{{--                                                        @if(Qs::userIsTeamSA())--}}
{{--                                                            <a href="{{ route('intakes.edit', $p['key']) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>--}}
{{--                                                        @endif--}}
{{--                                                        --}}{{--                                                            @if(Qs::userIsSuperAdmin())--}}
{{--                                                        --}}{{--                                                                <a id="{{ $p->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>--}}
{{--                                                        --}}{{--                                                                <form method="post" id="item-delete-{{ $p->id }}" action="{{ route('intakes.destroy', $p->id) }}" class="hidden">@csrf @method('delete')</form>--}}
{{--                                                        --}}{{--                                                            @endif--}}

{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                    </tr>--}}
{{--                                @endforeach--}}
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
