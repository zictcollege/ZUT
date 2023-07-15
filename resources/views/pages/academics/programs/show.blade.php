@extends('layouts.master')
@section('page_title', 'Program - '.$myprogram['program']->code)
@section('content')
    @php
        use App\Helpers\Qs;
    @endphp
    <div class="row">
        <div class="col-md-3 text-center">
            <div class="card">
                <div class="card-body">
                    <img style="width: 90%; height:90%" src="{{ '' }}" alt="photo" class="rounded-circle">
                    <br>
                    <h3 class="mt-3">{{ $myprogram['program']->name }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item">
                            <a href="#" class="nav-link active" >{{ $myprogram['program']->name }}</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        {{--Basic Info--}}
                        <div class="tab-pane fade show active" id="basic-info">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td class="font-weight-bold">Name</td>
                                    <td>{{ $myprogram['program']->name}}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Code</td>
                                    <td>{{ $myprogram['program']->code }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Qualification</td>
                                    <td>{{ $myprogram['program']->qualification->name }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Department</td>
                                    <td>{{ $myprogram['program']->department->name }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Description</td>
                                    <td>{{ $myprogram['program']->description }}</td>
                                </tr>

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Courses</h6>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        @foreach ($output as $program)
                            @foreach ($program['levels'] as $level)
                                <li class="nav-item">
                                    <a href="#all-{{ $level['level'] }}" class="nav-link{{ $loop->first ? ' active' : '' }}" data-toggle="tab">
                                        {{ $level['levelName'] }}
                                    </a>
                                </li>
                            @endforeach
                        @endforeach
                            <li class="nav-item">
                                <a href="#all-add-courses" class="nav-link" data-toggle="tab">
                                    Add Courses
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#all-add-prerequisite-courses" class="nav-link" data-toggle="tab">
                                    Add Prerequisite Courses
                                </a>
                            </li>
                    </ul>

                    <div class="tab-content">
                        @foreach ($output as $programs)
                            @foreach ($programs['levels'] as $level)
                                <div class="tab-pane fade{{ $loop->first ? ' show active' : '' }}" id="all-{{ $level['level'] }}">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Code</th>
                                            <th>Prerequisite</th> <!-- New column for prerequisites -->
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($level['courses'] as $course)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $course['course_name'] }}</td>
                                                <td>{{ $course['code'] }}</td>
                                                <td>
                                                    @if (count($course['prerequisites']) > 0)
                                                            @foreach ($course['prerequisites'] as $prerequisite)
                                                                {{ $prerequisite['prerequisite_code'].' '.$prerequisite['prerequisite_name'] }}
                                                            @endforeach
                                                    @else

                                                    @endif
                                                </td>
                                                <td>
                                                    @if (Qs::userIsSuperAdmin())
                                                        <a id="{{ $course['course_id'] }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                        <form method="post" id="item-delete-{{ $course['course_id'] }}" action="{{ route('destroy.programsCourse', ['programID' => $myprogram['program']->id, 'levelID' => $level['level'], 'courseID' => $course['course_id']]) }}" class="hidden">@csrf @method('delete')</form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        @endforeach

                        {{--                        @foreach ($output as $programs)--}}
{{--                            @foreach ($programs['levels'] as $level)--}}
{{--                                <div class="tab-pane fade{{ $loop->first ? ' show active' : '' }}" id="all-{{ $level['level'] }}">--}}
{{--                                    <table class="table table-bordered">--}}
{{--                                        <thead>--}}
{{--                                        <tr>--}}
{{--                                            <th>S/N</th>--}}
{{--                                            <th>Name</th>--}}
{{--                                            <th>Code</th>--}}
{{--                                            <th>Action</th>--}}
{{--                                        </tr>--}}
{{--                                        </thead>--}}
{{--                                        <tbody>--}}
{{--                                        @foreach ($level['courses'] as $course)--}}
{{--                                            <tr>--}}
{{--                                                <td>{{ $loop->iteration }}</td>--}}
{{--                                                <td>{{ $course['course_name'] }}</td>--}}
{{--                                                <td>{{ $course['code'] }}</td>--}}
{{--                                                <td>--}}
{{--                                                @if(Qs::userIsSuperAdmin())--}}
{{--                                                    <a id="{{ $course['course_id'] }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>--}}
{{--                                                    <form method="post" id="item-delete-{{ $course['course_id'] }}" action="{{ route('destroy.programsCourse', ['programID' => $myprogram['program']->id, 'levelID' => $level['level'], 'courseID' => $course['course_id']]) }}" class="hidden">@csrf @method('delete')</form>--}}
{{--                                                @endif--}}
{{--                                            </td>--}}
{{--                                            </tr>--}}
{{--                                        @endforeach--}}
{{--                                        </tbody>--}}
{{--                                    </table>--}}
{{--                                </div>--}}
{{--                            @endforeach--}}
{{--                        @endforeach--}}
                            <div class="tab-pane fade" id="all-add-courses">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-info border-0 alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>

                                            <span>When a class is created, a Section will be automatically created for the class, you can edit it or add more sections to the class at <a target="_blank" href="{{ 00 }}">Manage Sections</a></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <form class="ajax-store" method="post" action="{{ route('store.courses') }}">
                                            @csrf
                                            <div class="form-group row">
                                                <label for="courses" class="col-lg-3 col-form-label font-weight-semibold">Courses <span class="text-danger">*</span></label>
                                                <div class="col-lg-9">
                                                    <select required data-placeholder="Select Course" multiple class="form-control select-search" name="courseID[]" id="courses">
                                                            <option value=""></option>
                                                        @foreach($myprogram['newcourses'] as $c)
                                                            <option value="{{ $c->id }}">{{ $c->code.' - '.$c->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <input type="hidden" name="programID" value="{{$myprogram['program']->id}}">

                                            <div class="form-group row">
                                                <label for="course-level" class="col-lg-3 col-form-label font-weight-semibold">Level <span class="text-danger">*</span></label>
                                                <div class="col-lg-9">
                                                    <select required data-placeholder="Select Class Type" class="form-control select" name="level_id" id="course-level">
                                                        @foreach($myprogram['levels'] as $l)
                                                            <option value="{{ $l->id }}">{{ $l->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <button id="ajax-btn" type="submit" class="btn btn-primary">Submit form <i class="icon-paperplane ml-2"></i></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="all-add-prerequisite-courses">
                                <div class="row">
                                    <div class="col-md-6">
                                        <form class="ajax-store" method="post" action="{{ route('store.prerequisite') }}">
                                            @csrf
                                            <div class="form-group row">
                                                <label for="courses" class="col-lg-3 col-form-label font-weight-semibold">Courses <span class="text-danger">*</span></label>
                                                <div class="col-lg-9">
                                                    <select required data-placeholder="Select Course" class="form-control select-search" name="courseID" id="courses">
                                                        <option value=""></option>
                                                        @foreach($myprogram['pcourses'] as $c)
                                                            <option value="{{ $c->id }}">{{ $c->code.' - '.$c->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="course-level" class="col-lg-3 col-form-label font-weight-semibold">Prerequisite Courses <span class="text-danger">*</span></label>
                                                <div class="col-lg-9">
                                                    <select required data-placeholder="Select Prerequisite" multiple  class="form-control select" name="prerequisiteID[]" id="course-level">
                                                        @foreach($myprogram['pcourses'] as $c)
                                                            <option value="{{ $c->id }}">{{ $c->code.' - '.$c->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <button id="ajax-btn" type="submit" class="btn btn-primary">Submit form <i class="icon-paperplane ml-2"></i></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        {{--Program courses and level--}}





@endsection
