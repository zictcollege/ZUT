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
                    <img style="width: 90%; height:90%" src="{{ 00 }}" alt="photo" class="rounded-circle">
                    <br>
                    <h3 class="mt-3">{{ $period['period']->code }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card card-collapsed">
                    <div class="card-header header-elements-inline">
                        <h6 class="card-title">Academic Period Infor</h6>
                        {!! Qs::getPanelOptions() !!}
                    </div>
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item">
                            <a href="#basic-info" class="nav-link active" data-toggle="tab" >Academic Period Details </a>
                        </li>
                        <li class="nav-item">
                            <a href="#all-fees" class="nav-link" data-toggle="tab" >Fees</a>
                        </li>
                        <li class="nav-item">
                            <a href="#new-fees" class="nav-link" data-toggle="tab" >Add Fees</a>
                        </li>
                        <li class="nav-item">
                            <a href="#new-class" class="nav-link" data-toggle="tab" >New Class</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        {{--                        Basic Info--}}
                        <div class="tab-pane fade show active" id="basic-info">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td class="font-weight-bold">Academic Period Code</td>
                                    <td>{{ $period['period']->code }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Start date</td>
                                    <td>{{ $period['period']->acStartDate }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">End date</td>
                                    <td>{{ $period['period']->acEndDate }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Registration End date</td>
                                    <td>{{ $period['period']->registrationDate }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Late registration End Date</td>
                                    <td>{{ $period['period']->lateRegistrationDate }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Registration Threshold</td>
                                    <td>{{ $period['period']->registrationThreshold}}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">View Results Threshold</td>
                                    <td>{{ $period['period']->resultsThreshold  }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Exam Threshold Threshold</td>
                                    <td>{{ $period['period']->examSlipThreshold }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Study Mode</td>
                                    <td>{{ $period['period']->studyMode->name }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">type</td>
                                    <td>{{ $period['period']->periodType->name }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Intake</td>
                                    <td>{{ $period['period']->intake->name }}</td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade show" id="all-fees">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                    <th>Fee type</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($period['periodFees'] as $fees)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $fees->fee_name }}</td>
                                        <td>{{ $fees->amount  }}</td>
                                        <td>
                                            @if($fees->once_off == 1)
                                            {{ 'Once Off Fee' }}
                                            @elseif($fees->normal == 1)
                                            {{ 'Recurring Fee' }}
                                            @elseif($fees->repeat == 1)
                                            {{ 'Repeat Course Fee' }}
                                            @endif

                                        </td>
                                        <td>
                                            @if (Qs::userIsSuperAdmin())
                                                                                                        <a id="{{ $fees->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                                                                        <form method="post" id="item-delete-{{ $fees->id }}" action="{{ route('destroy.academic.fees', $fees->id) }}" class="hidden">@csrf @method('delete')</form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade show" id="new-fees">
                            <div class="row">
                                <div class="col-md-6">
                                    <form class="ajax-store" method="post" action="{{ route('add.fees') }}">
                                        @csrf
                                        <div class="form-group row">
                                            <label for="fee_id" class="col-lg-3 col-form-label font-weight-semibold">Fee Name <span class="text-danger">*</span></label>
                                            <div class="col-lg-9">
                                                <select required data-placeholder="Select Fee" class="form-control select" name="feeID" id="fee_id">
                                                    <option>Select Fee</option>
                                                    @foreach($period['fees'] as $f)
                                                        <option  value="{{ $f->id }}">{{ $f->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label font-weight-semibold">Amount <span class="text-danger">*</span></label>
                                            <div class="col-lg-9">
                                                <input name="amount" value="{{ old('amount') }}" required type="text" class="form-control" placeholder="Amount">
                                                <input name="academicPeriodID" value="{{ $period['period']->id }}"  type="hidden">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label font-weight-semibold">Type <span class="text-danger">*</span></label>
                                            <div class="col-lg-9">
                                                <select required data-placeholder="Select Fee" class="form-control select" name="feetype" id="fee_id">
                                                    <option>Select Fee</option>
                                                        <option value="0">Recurring</option>
                                                    <option value="1">Once Off</option>
                                                    <option value="2">Repeat Course Fee</option>
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
                        <div class="tab-pane fade show" id="new-class">
                            <div class="row">
                                <div class="col-md-6">
                                    <form class="ajax-store" method="post" action="{{ route('store.classes') }}">
                                        @csrf
                                        <div class="form-group row">
                                            <label for="instructor" class="col-lg-3 col-form-label font-weight-semibold">Instructor <span class="text-danger">*</span></label>
                                            <div class="col-lg-9">
                                                <input type="hidden" name="academicPeriodID" value="{{$period['period']->id}}">
                                                <select required data-placeholder="Select Course" class="form-control select-search" name="instructorID" id="courses">
                                                    <option value=""></option>
                                                    @foreach($period['instructors'] as $u)
                                                        <option value="{{ $u->id }}">{{ $u->first_name.' '.$u->last_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="course-level" class="col-lg-3 col-form-label font-weight-semibold">Course <span class="text-danger">*</span></label>
                                            <div class="col-lg-9">
                                                <select required data-placeholder="Select Prerequisite" class="form-control select-search" name="courseID" id="course-level">
                                                    <option value=""></option>
                                                    @foreach($period['courses'] as $c)
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

                @foreach ($output as $program)
                    <div class="card card-collapsed">
                        <div class="card-header header-elements-inline">
                            <h6 class="card-title">{{ $program['program'] }}</h6>
                            {!! Qs::getPanelOptions() !!}
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-tabs-highlight">
                                @foreach ($program['levels'] as $level)
                                    <li class="nav-item">
                                        <a href="#all-{{ $level['level_id'].$program['program_id'] }}" class="nav-link{{ $loop->first ? ' active' : '' }}" data-toggle="tab">
                                            {{ $level['level'] }}
                                        </a>
                                    </li>
                                @endforeach
                                {{--                        <li class="nav-item">--}}
                                {{--                            <a href="#all-add-class" class="nav-link" data-toggle="tab">--}}
                                {{--                                Add Courses--}}
                                {{--                            </a>--}}
                                {{--                        </li>--}}
                            </ul>

                            <div class="tab-content">
                                {{--                        @foreach ($output as $programs)--}}
                                @foreach ($program['levels'] as $level)
                                    <div class="tab-pane fade{{ $loop->first ? ' show active' : '' }}" id="all-{{ $level['level_id'].$program['program_id'] }}">
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Course</th>
                                                <th>Instructor</th>
                                                <th>Prerequisite</th> <!-- New column for prerequisites -->
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($level['courses'] as $course)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $course['code'].' '.$course['course_name'] }}</td>
                                                    <td>{{ $course['instructor'] }}</td>
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
                                                            <a id="{{ $course['class_id'] }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                            <form method="post" id="item-delete-{{ $course['class_id'] }}" action="{{ route('classes.delete', $course['class_id']) }}" class="hidden">@csrf @method('delete')</form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endforeach
                                {{--                        @endforeach--}}

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
                                                            {{--                                                    @foreach($myprogram['newcourses'] as $c)--}}
                                                            {{--                                                        <option value="{{ $c->id }}">{{ $c->code.'a - '.$c->name }}</option>--}}
                                                            {{--                                                    @endforeach--}}
                                                        </select>
                                                    </div>
                                                </div>
                                                {{--                                        <input type="hidden" name="programID" value="{{$myprogram['program']->id}}">--}}

                                                <div class="form-group row">
                                                    <label for="course-level" class="col-lg-3 col-form-label font-weight-semibold">Level <span class="text-danger">*</span></label>
                                                    <div class="col-lg-9">
                                                        <select required data-placeholder="Select Class Type" class="form-control select" name="level_id" id="course-level">
                                                            {{--                                                    @foreach($myprogram['levels'] as $l)--}}
                                                            {{--                                                        <option value="{{ $l->id }}">{{ $l->name }}</option>--}}
                                                            {{--                                                    @endforeach--}}
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
                @endforeach
            </div>

        </div>

@endsection
