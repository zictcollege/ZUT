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
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-9 p-0">
                                    <h4>{{ $users['currentProgramName'] }}</h4>
                                    <p>Select the courses that you wish to be exempted in. Thereafter, you will need to upload your Academic & or your Professional qualifications that will support this Exemption Application.</p>
                                    <h5>Select Multiple Courses</h5>
                                </div>
                                <div class="col-md-3 text-right mb-2 p-0">
                                    <button type="button" class="btn btn-primary send-mail btn-sm" disabled="disabled"> <i class="fa fa-share"></i> Exempt</button>
                                </div>
                                <div class="col-md-12 success-mail p-0" style="display: none;">
                                    <div class="alert alert-success">
                                        Courses Exempted Successfully.
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th><label>
                                                <input type="checkbox" value="1" name="user-all" class="user-all">
                                            </label></th>
                                        <th>Course</th>
                                        <th>Course Name</th>
{{--                                        <th>Email</th>--}}
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($users['currentRegisteredClasses'])
                                        @foreach($users['currentRegisteredClasses'] as $key => $value)
                                            <tr>
                                                <td>
                                                    {{ Form::checkbox('ckeck_user', 1, false,['class'=>'ckeck_user','data-id' => $value['key'] ]) }}
                                                </td>
                                                <td>{{ $value['course_code'] }}</td>
                                                <td>{{ $value['course_name'] }}</td>
{{--                                                <td>{{ $value->email }}</td>--}}
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--Class List Ends--}}

@endsection
