@extends('layouts.master')
@section('page_title', 'Manage Academics')
@section('content')
    @php
        use App\Helpers\Qs;
    @endphp

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Manage Academic Periods</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                @if(true)
                <li class="nav-item"><a href="#all-open" class="nav-link" data-toggle="tab">Open Academic Periods</a></li>
                    <li class="nav-item"><a href="#all-closed" class="nav-link" data-toggle="tab">Closed Academic Periods</a></li>
                    <li class="nav-item"><a href="#add-tt" class="nav-link active" data-toggle="tab">Create Academic Year</a></li>
                @endif
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Show Academic Periods</a>
                    <div class="dropdown-menu dropdown-menu-right">
{{--                        @foreach($acperiods as $mc)--}}
{{--                            <a href="#ttr{{ $mc->id }}" class="dropdown-item" data-toggle="tab">{{ $mc->code }}</a>--}}
{{--                        @endforeach--}}
                    </div>
                </li>
            </ul>


            <div class="tab-content">

                @if(true)
                <div class="tab-pane fade show active" id="add-tt">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>

                                <span>When a an Academic year is created, go ahead and create intakes and programs running there <a target="_blank" href="{{ '#' }}">Manage Sections</a></span>
                            </div>
                        </div>
                    </div>

                   <div class="col-md-8">
                       <form class="ajax-store" method="post" action="{{ route('create') }}">
                           @csrf
                           <div class="form-group row">
                               <label class="col-lg-3 col-form-label font-weight-semibold">Code <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                   <input name="code" value="{{ old('code') }}" required type="text" class="form-control" placeholder="code">
                               </div>
                           </div>

                           <div class="form-group row">
                               <label for="my_class_id" class="col-lg-3 col-form-label font-weight-semibold">Start Date <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                   <input autocomplete="off" name="acStartDate" value="{{ old('acStartDate') }}" type="text" class="form-control date-pick" placeholder="Academic Start Date">

                               </div>
                           </div>

                           <div class="form-group row">
                               <label for="exam_id" class="col-lg-3 col-form-label font-weight-semibold">End date <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                       <input autocomplete="off" name="acEndDate" value="{{ old('acEndDate') }}" type="text" class="form-control date-pick" placeholder="Select Date...">

                               </div>
                           </div>
                           <div class="form-group row">
                               <label for="exam_id" class="col-lg-3 col-form-label font-weight-semibold">Registration End date <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                   <input autocomplete="off" name="registrationDate" value="{{ old('registrationDate') }}" type="text" class="form-control date-pick" placeholder="Select Date...">

                               </div>
                           </div>
                           <div class="form-group row">
                               <label for="exam_id" class="col-lg-3 col-form-label font-weight-semibold">Late Registration End date <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                   <input autocomplete="off" name="lateRegistrationDate" value="{{ old('lateRegistrationDate') }}" type="text" class="form-control date-pick" placeholder="Select Date...">
                               </div>
                           </div>
                           <div class="form-group row">
                               <label class="col-lg-3 col-form-label font-weight-semibold">Registration Threshold <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                   <input name="registrationThreshold" value="{{ old('registrationThreshold') }}" required type="text" class="form-control" placeholder="%">
                               </div>
                           </div>
                           <div class="form-group row">
                               <label class="col-lg-3 col-form-label font-weight-semibold">View Results Threshold <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                   <input name="resultsThreshold" value="{{ old('resultsThreshold') }}" required type="text" class="form-control" placeholder="%">
                               </div>
                           </div>
                           <div class="form-group row">
                               <label class="col-lg-3 col-form-label font-weight-semibold">Download Exam Slip Threshold <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                   <input name="examSlipThreshold" value="{{ old('examSlipThreshold') }}" required type="text" class="form-control" placeholder="%">
                               </div>
                           </div>
                           <div class="form-group row">
                               <label for="exam_id" class="col-lg-3 col-form-label font-weight-semibold">Period ID <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                   <input autocomplete="off" name="periodID" value="{{ old('periodID') }}" type="text" class="form-control" placeholder="Select Date...">
                               </div>
                           </div>
                           <div class="form-group row">
                               <label for="study-mode" class="col-lg-3 col-form-label font-weight-semibold">Select Study Mode <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                   <select required data-placeholder="Select Class" class="form-control select" name="studyModeIDAllowed" id="study-mode">
                                       <option value="">Choose .....</option>
                                       @foreach($studymode as $c)
                                           <option {{ old('id') == $c->id ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->name }}</option>
                                       @endforeach
                                   </select>
                               </div>
                           </div>
                           <div class="form-group row">
                               <label for="period-type" class="col-lg-3 col-form-label font-weight-semibold">Academic Period type <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                   <select required data-placeholder="Select Class" class="form-control select" name="type" id="period-type">
                                       <option value="">Choose .....</option>
                                      @foreach($periodstypes as $c)
                                          <option {{ old('id') == $c->id ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->name }}</option>
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
                @endif

                        <div class="tab-pane fade" id="all-open">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-info border-0 alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>

                                        <span>When a an Academic year is created, go ahead and create intakes and programs running there <a target="_blank" href="{{ '#' }}">Manage Sections</a></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <table class="table datatable-button-html5-columns">
                                    <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Code</th>
                                        <th>Reg Date</th>
                                        <th>Late Date</th>
                                        <th>Start</th>
                                        <th>End</th>
                                        <th>Mode</th>
                                        <th>type</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($open as $m)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $m->code }}</td>
                                            <td>{{ $m->registrationDate}}</td>
                                            <td>{{ $m->lateRegistrationDate}}</td>
                                            <td>{{ $m->acStartDate}}</td>
                                            <td>{{ $m->acEndDate}}</td>
                                            <td>{{ $m->studyMode->name}}</td>
                                            <td>{{ $m->periodType->name}}</td>
                                            <td class="text-center">
                                                <div class="list-icons">
                                                    <div class="dropdown">
                                                        <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                            <i class="icon-menu9"></i>
                                                        </a>

                                                        <div class="dropdown-menu dropdown-menu-left">
                                                            @if(true)
                                                                <a href="{{ route('update',Qs::hash($m->id)) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                                            @endif
                                                                @if(true)
                                                                    <a href="{{ route('academic.show', Qs::hash($m->id)) }}" class="dropdown-item"><i class="icon-eye"></i> Show</a>
                                                                @endif
                                                            @if(true)
                                                                <a id="{{ $m->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                            @endif

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
                    <div class="tab-pane fade" id="all-closed">
                            <div class="col-md-12">
                                <table class="table datatable-button-html5-columns">
                                    <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Code</th>
                                        <th>Registration</th>
                                        <th>Late Date</th>
                                        <th>Description</th>
                                        <th>Description</th>
                                        <th>Description</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($closed as $m)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $m->code }}</td>
                                            <td>{{ $m->registrationDate}}</td>
                                            <td>{{ $m->lateRegistrationDate}}</td>
                                            <td>{{ $m->acStartDate}}</td>
                                            <td>{{ $m->acEndDate}}</td>
                                            <td>{{ $m->studyMode->name}}</td>
                                            <td>{{ $m->periodType->name}}</td>
                                            <td class="text-center">
                                                <div class="list-icons">
                                                    <div class="dropdown">
                                                        <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                            <i class="icon-menu9"></i>
                                                        </a>

                                                        <div class="dropdown-menu dropdown-menu-left">
                                                            @if(true)
                                                                <a href="{{ route('studymodes.edit', Qs::hash($m->id)) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                                            @endif
                                                                @if(true)
                                                                    <a href="{{ route('academic.show', Qs::hash($m->id)) }}" class="dropdown-item"><i class="icon-eye"></i> Show</a>
                                                                @endif
                                                            @if(true)
                                                                <a id="{{ $m->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                                <form method="post" id="item-delete-{{ $m->id }}" action="{{ route('academics.destroy', Qs::hash($m->id)) }}" class="hidden">@csrf @method('delete')</form>
                                                            @endif

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
{{--                @foreach($my_classes as $mc)--}}
{{--                    <div class="tab-pane fade" id="ttr{{ $mc->id }}">                         <table class="table datatable-button-html5-columns">--}}
{{--                            <thead>--}}
{{--                            <tr>--}}
{{--                                <th>S/N</th>--}}
{{--                                <th>Name</th>--}}
{{--                                <th>Class</th>--}}
{{--                                <th>Type</th>--}}
{{--                                <th>Year</th>--}}
{{--                                <th>Action</th>--}}
{{--                            </tr>--}}
{{--                            </thead>--}}
{{--                            <tbody>--}}
{{--                            @foreach($tt_records->where('my_class_id', $mc->id) as $ttr)--}}
{{--                                <tr>--}}
{{--                                    <td>{{ $loop->iteration }}</td>--}}
{{--                                    <td>{{ $ttr->name }}</td>--}}
{{--                                    <td>{{ $ttr->my_class->name }}</td>--}}
{{--                                    <td>{{ ($ttr->exam_id) ? $ttr->exam->name : 'Class TimeTable' }}--}}
{{--                                    <td>{{ $ttr->year }}</td>--}}
{{--                                    <td class="text-center">--}}
{{--                                        <div class="list-icons">--}}
{{--                                            <div class="dropdown">--}}
{{--                                                <a href="#" class="list-icons-item" data-toggle="dropdown">--}}
{{--                                                    <i class="icon-menu9"></i>--}}
{{--                                                </a>--}}

{{--                                                <div class="dropdown-menu dropdown-menu-right">--}}
{{--                                                    View--}}
{{--                                                    <a href="{{ route('ttr.show', $ttr->id) }}" class="dropdown-item"><i class="icon-eye"></i> View</a>--}}

{{--                                                    @if(Qs::userIsTeamSA())--}}
{{--                                                    Manage--}}
{{--                                                    <a href="{{ route('ttr.manage', $ttr->id) }}" class="dropdown-item"><i class="icon-plus-circle2"></i> Manage</a>--}}
{{--                                                    Edit--}}
{{--                                                    <a href="{{ route('ttr.edit', $ttr->id) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>--}}
{{--                                                    @endif--}}

{{--                                                    Delete--}}
{{--                                                    @if(Qs::userIsSuperAdmin())--}}
{{--                                                        <a id="{{ $ttr->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>--}}
{{--                                                        <form method="post" id="item-delete-{{ $ttr->id }}" action="{{ route('ttr.destroy', $ttr->id) }}" class="hidden">@csrf @method('delete')</form>--}}
{{--                                                    @endif--}}

{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </td>--}}
{{--                                </tr>--}}
{{--                            @endforeach--}}

{{--                            </tbody>--}}
{{--                        </table>--}}
{{--                    </div>--}}
{{--                @endforeach--}}

{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
@endsection
