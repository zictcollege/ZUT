@extends('layouts.master')
@section('page_title', 'Academic - '.$period['period']->code)
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
                    <h3 class="mt-3">{{ $period['period']->code }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-9">

            <div class="card card-collapsed">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Programs</h6>
                    {!! Qs::getPanelOptions() !!}
                </div>

                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item"><a href="#all-programsList" class="nav-link active" data-toggle="tab">Manage Intakes</a></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="all-programsList">
                            <table class="table datatable-button-html5-columns">
                                <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Code</th>
                                    <th>Qualification</th>
                                    <th>Program</th>
                                    <th>Department</th>
                                    <th>Enrolled Students</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($ap['programs'] as $p)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $p['code'] }}</td>
                                        <td>{{ $p['name'] }}</td>
                                        <td>{{ $p['qualification'] }}</td>
                                        <td>{{ $p['department'] }}</td>
                                        <td>{{ $p['enrolledStudents'] }}</td>
                                        <td class="text-center">
                                            <div class="list-icons">
                                                <div class="dropdown">
                                                    <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                        <i class="icon-menu9"></i>
                                                    </a>

                                                    <div class="dropdown-menu dropdown-menu-left">
                                                        @if(Qs::userIsTeamSA())
                                                            <a href="{{ route('intakes.edit', $p['id']) }}" class="dropdown-item"><i class="icon-eye"></i> Show</a>
                                                        @endif
                                                        {{--                                                            @if(Qs::userIsSuperAdmin())--}}
                                                        {{--                                                                <a id="{{ $p->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>--}}
                                                        {{--                                                                <form method="post" id="item-delete-{{ $p->id }}" action="{{ route('intakes.destroy', $p->id) }}" class="hidden">@csrf @method('delete')</form>--}}
                                                        {{--                                                            @endif--}}

                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

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
                                @foreach($ap['classes'] as $p)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $p['course_code'] }}</td>
                                        <td>{{ $p['course_name'] }}</td>
                                        <td>{{ $p['enrolledStudentsCount'] }}</td>
                                        <td>{{ $p['instructor'] }}</td>
                                        <td class="text-center">
                                            <div class="list-icons">
                                                <div class="dropdown">
                                                    <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                        <i class="icon-menu9"></i>
                                                    </a>

                                                    <div class="dropdown-menu dropdown-menu-left">
                                                        @if(Qs::userIsTeamSA())
                                                            <a href="{{ route('intakes.edit', $p['key']) }}" class="dropdown-item"><i class="icon-eye"></i> Show</a>
                                                        @endif
                                                        {{--                                                            @if(Qs::userIsSuperAdmin())--}}
                                                        {{--                                                                <a id="{{ $p->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>--}}
                                                        {{--                                                                <form method="post" id="item-delete-{{ $p->id }}" action="{{ route('intakes.destroy', $p->id) }}" class="hidden">@csrf @method('delete')</form>--}}
                                                        {{--                                                            @endif--}}

                                                    </div>
                                                </div>
                                            </div>
                                        </td>
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
