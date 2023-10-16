@extends('layouts.master')
@section('page_title', 'Change program')
@section('content')
    @php
        use App\Helpers\Qs;
    @endphp

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Change Program Applications</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#my-applied-programs" class="nav-link active" data-toggle="tab">Manage my Applications</a></li>
                <li class="nav-item"><a href="#new-class" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> Create New Applications</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="my-applied-programs">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>

                                <span>You can apply to change your program of study by clicking on the “Create New Application” button on your top right.
                                    In order for your application to be valid, you need to meet the following conditions:</span>
                                <ol>
                                    <li>Application can only be valid two weeks after the end of the sets registration date of the current academic period.</li>
                                    <li>Application will only be approved if a reason provided is valid.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <table class="table datatable-button-html5-columns">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Intake</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
{{--                        @foreach($intakes as $i)--}}
{{--                            <tr>--}}
{{--                                <td>{{ $loop->iteration }}</td>--}}
{{--                                <td>{{ $i->name }}</td>--}}
{{--                                <td class="text-center">--}}
{{--                                    <div class="list-icons">--}}
{{--                                        <div class="dropdown">--}}
{{--                                            <a href="#" class="list-icons-item" data-toggle="dropdown">--}}
{{--                                                <i class="icon-menu9"></i>--}}
{{--                                            </a>--}}

{{--                                            <div class="dropdown-menu dropdown-menu-left">--}}
{{--                                                @if(Qs::userIsTeamSA())--}}
{{--                                                    <a href="{{ route('intakes.edit', $i->id) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>--}}
{{--                                                @endif--}}
{{--                                                @if(Qs::userIsSuperAdmin())--}}
{{--                                                    <a id="{{ $i->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>--}}
{{--                                                    <form method="post" id="item-delete-{{ $i->id }}" action="{{ route('intakes.destroy', $i->id) }}" class="hidden">@csrf @method('delete')</form>--}}
{{--                                                @endif--}}

{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        @endforeach--}}
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane fade" id="new-class">
                    <div class="row">
                        <div class="col-md-6">
                            <form class="ajax-store" method="post" action="{{ route('change_program_apply')  }}">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Select Qualification <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select required data-placeholder="Select Course" class="form-control select-search" name="qualification" id="courses">
                                            <option value=""></option>
{{--                                            @foreach($period['instructors'] as $u)--}}
{{--                                                <option value="{{ $u->id }}">{{ $u->first_name.' '.$u->last_name }}</option>--}}
{{--                                            @endforeach--}}
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Prefered Program of Choice<span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select required data-placeholder="Select Course" class="form-control select-search" name="qualification" id="courses">
                                            <option value=""></option>
                                            {{--                                            @foreach($period['instructors'] as $u)--}}
                                            {{--                                                <option value="{{ $u->id }}">{{ $u->first_name.' '.$u->last_name }}</option>--}}
                                            {{--                                            @endforeach--}}
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Study Mode <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select required data-placeholder="Select Course" class="form-control select-search" name="qualification" id="courses">
                                            <option value=""></option>
                                            {{--                                            @foreach($period['instructors'] as $u)--}}
                                            {{--                                                <option value="{{ $u->id }}">{{ $u->first_name.' '.$u->last_name }}</option>--}}
                                            {{--                                            @endforeach--}}
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Reason for change<span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select required data-placeholder="Select Course" class="form-control select-search" name="qualification" id="courses">
                                            <option value=""></option>
                                            {{--                                            @foreach($period['instructors'] as $u)--}}
                                            {{--                                                <option value="{{ $u->id }}">{{ $u->first_name.' '.$u->last_name }}</option>--}}
                                            {{--                                            @endforeach--}}
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Explanation for other<span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <textarea class="form-text form-control" name="reason" type="text" rows="3"></textarea>
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

    {{--Class List Ends--}}

@endsection
