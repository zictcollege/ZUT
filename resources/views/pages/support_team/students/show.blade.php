@extends('layouts.master')
@section('page_title', 'Student Profile - '.$data['userinfo']->first_name)
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
                <h3 class="mt-3">{{ $data['userinfo']->first_name.' '.$data['userinfo']->last_name }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card card-collapsed">
            <div class="card-header header-elements-inline">
                <h6 class="card-title">Account</h6>
                {!! Qs::getPanelOptions() !!}
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-highlight">
                    <li class="nav-item">
                        <a href="#account-info" class="nav-link active" data-toggle="tab">{{ 'Account Details' }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="#profile-info" class="nav-link" data-toggle="tab" >{{ 'Profile Details' }}</a>
                    </li>
                </ul>

                <div class="tab-content">
                    {{--Basic Info--}}
                    <div class="tab-pane fade show active" id="account-info">
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td class="font-weight-bold">Student ID</td>
                                <td>{{ $data['student_id']}}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-justify">Year of Study</td>
                                <td>{{ $data['levels']->name }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-justify">Academic Year</td>
                                <td>{{ 'To be Updated' }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-justify">Study Category</td>
                                <td>{{ $data['studyMode']->name }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-justify">Status</td>
                                <td>{{ 'To be updated' }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-justify">Semester</td>
                                <td>
                                    @php
                                        $intakeID = $data['intakeID'];
                                        $currentMonth = date('m');
                                    @endphp

                                    @if (($intakeID == 1 && $currentMonth > 6) || ($intakeID == 2 && $currentMonth > 12))
                                        {{ 2 }}
                                    @else
                                        {{ 1 }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-justify">Programme Name</td>
                                <td>{{ $data['program']->name  }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-justify">Programme Code</td>
                                <td>{{ $data['program']->code }}</td>
                            </tr>
                            {{--                        <tr>--}}
                            {{--                            <td class="font-weight-bold text-justify">Study Mode</td>--}}
                            {{--                            <td>{{ $period['period']->studyMode->name }}</td>--}}
                            {{--                        </tr>--}}
                            {{--                        <tr>--}}
                            {{--                            <td class="font-weight-bold text-justify">type</td>--}}
                            {{--                            <td>{{ $period['period']->periodType->name }}</td>--}}
                            {{--                        </tr>--}}
                            <tr>
                                <td class="font-weight-bold text-justify">Intake</td>
                                <td>{{ $data['intake']->name }}</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>

                    <div class="tab-pane fade show" id="profile-info">
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td class="font-weight-bold">Gender</td>
                                <td>{{ $data['userinfo']->gender }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-justify">Email</td>
                                <td>{{ $data['userinfo']->email }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-justify">Academic Year</td>
                                <td>{{ 'To be Updated' }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-justify">Study Category</td>
                                <td>{{ $data['studyMode']->name }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-justify">Status</td>
                                <td>{{ 'To be updated' }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-justify">Semester</td>
                                <td>
                                    @php
                                        $intakeID = $data['intakeID'];
                                        $currentMonth = date('m');
                                    @endphp

                                    @if (($intakeID == 1 && $currentMonth > 6) || ($intakeID == 2 && $currentMonth > 12))
                                        {{ 2 }}
                                    @else
                                        {{ 1 }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-justify">Programme Name</td>
                                <td>{{ $data['program']->name  }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-justify">Programme Code</td>
                                <td>{{ $data['program']->code }}</td>
                            </tr>
                            {{--                        <tr>--}}
                            {{--                            <td class="font-weight-bold text-justify">Study Mode</td>--}}
                            {{--                            <td>{{ $period['period']->studyMode->name }}</td>--}}
                            {{--                        </tr>--}}
                            {{--                        <tr>--}}
                            {{--                            <td class="font-weight-bold text-justify">type</td>--}}
                            {{--                            <td>{{ $period['period']->periodType->name }}</td>--}}
                            {{--                        </tr>--}}
                            <tr>
                                <td class="font-weight-bold text-justify">Intake</td>
                                <td>{{ $data['intake']->name }}</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-collapsed">
            <div class="card-header header-elements-inline">
                <h6 class="card-title">Money</h6>
                {!! Qs::getPanelOptions() !!}
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-highlight">
                    <li class="nav-item">
                        <a href="#" class="nav-link active">{{ 'Financials' }}</a>
                    </li>
                </ul>

                <div class="tab-content">
                    {{--Basic Info--}}
                    {{--                    <div class="tab-pane fade show active" id="basic-info">--}}
                    {{--                        <table class="table table-bordered">--}}
                    {{--                            <tbody>--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Name</td>--}}
                    {{--                                <td>{{ $sr->user->name }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">ADM_NO</td>--}}
                    {{--                                <td>{{ $sr->adm_no }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Class</td>--}}
                    {{--                                <td>{{ $sr->my_class->name.' '.$sr->section->name }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @if($sr->my_parent_id)--}}
                    {{--                                <tr>--}}
                    {{--                                    <td class="font-weight-bold">Parent</td>--}}
                    {{--                                    <td>--}}
                    {{--                                        <span><a target="_blank" href="{{ route('users.show', Qs::hash($sr->my_parent_id)) }}">{{ $sr->my_parent->name }}</a></span>--}}
                    {{--                                    </td>--}}
                    {{--                                </tr>--}}
                    {{--                            @endif--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Year Admitted</td>--}}
                    {{--                                <td>{{ $sr->year_admitted }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Gender</td>--}}
                    {{--                                <td>{{ $sr->user->gender }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Address</td>--}}
                    {{--                                <td>{{ $sr->user->address }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @if($sr->user->email)--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Email</td>--}}
                    {{--                                <td>{{$sr->user->email }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @endif--}}
                    {{--                            @if($sr->user->phone)--}}
                    {{--                                <tr>--}}
                    {{--                                    <td class="font-weight-bold">Phone</td>--}}
                    {{--                                    <td>{{$sr->user->phone.' '.$sr->user->phone2 }}</td>--}}
                    {{--                                </tr>--}}
                    {{--                            @endif--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Birthday</td>--}}
                    {{--                                <td>{{$sr->user->dob }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @if($sr->user->bg_id)--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Blood Group</td>--}}
                    {{--                                <td>{{$sr->user->blood_group->name }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @endif--}}
                    {{--                            @if($sr->user->nal_id)--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Nationality</td>--}}
                    {{--                                <td>{{$sr->user->nationality->name }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @endif--}}
                    {{--                            @if($sr->user->state_id)--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">State</td>--}}
                    {{--                                <td>{{$sr->user->state->name }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @endif--}}
                    {{--                            @if($sr->user->lga_id)--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">LGA</td>--}}
                    {{--                                <td>{{$sr->user->lga->name }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @endif--}}
                    {{--                            @if($sr->dorm_id)--}}
                    {{--                                <tr>--}}
                    {{--                                    <td class="font-weight-bold">Dormitory</td>--}}
                    {{--                                    <td>{{$sr->dorm->name.' '.$sr->dorm_room_no }}</td>--}}
                    {{--                                </tr>--}}
                    {{--                            @endif--}}

                    {{--                            </tbody>--}}
                    {{--                        </table>--}}
                    {{--                    </div>--}}

                </div>
            </div>
        </div>
    </div>
</div>

    <div class="card card-collapsed">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Registration</h6>
            {!! Qs::getPanelOptions() !!}
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item">
                    <a href="#" class="nav-link active">{{ 'Registration' }}</a>
                </li>
            </ul>

            <div class="tab-content">
                {{--Basic Info--}}
              <div class="tab-pane fade show active" id="basic-info">
                  <div class="row justify-content-between">
                      <div class="col-md-5">
                          <h6 class="card-title text-center">Invoice</h6>
                          <table class="table table-striped table-striped-columns">
                              <thead>
                              <tr>
                                  <th>Name</th>
                                  <th>Amount (K)</th>
                              </tr>
                              </thead>
                              <tbody>
{{--                              @foreach($type as $m)--}}
{{--                                  <tr>--}}
{{--                                      <td>{{ $loop->iteration }}</td>--}}
{{--                                      <td>{{ $m->name }}</td>--}}
{{--                                      <td>{{ $m->description}}</td>--}}
{{--                                      <td class="text-center">--}}
{{--                                          <div class="list-icons">--}}
{{--                                              <div class="dropdown">--}}
{{--                                                  <a href="#" class="list-icons-item" data-toggle="dropdown">--}}
{{--                                                      <i class="icon-menu9"></i>--}}
{{--                                                  </a>--}}

{{--                                                  <div class="dropdown-menu dropdown-menu-left">--}}
{{--                                                      @if(true)--}}
{{--                                                          Edit--}}
{{--                                                          <a href="{{ route('studymodes.edit', $m->id) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>--}}
{{--                                                      @endif--}}
{{--                                                      @if(true)--}}
{{--                                                          Delete--}}
{{--                                                          <a id="{{ $m->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>--}}
{{--                                                          <form method="post" id="item-delete-{{ $m->id }}" action="{{ route('periodtypes.destroy', $m->id) }}" class="hidden">@csrf @method('delete')</form>--}}
{{--                                                      @endif--}}

{{--                                                  </div>--}}
{{--                                              </div>--}}
{{--                                          </div>--}}
{{--                                      </td>--}}
{{--                                  </tr>--}}
{{--                              @endforeach--}}
                              </tbody>
                          </table>
                          <div>
                              <div class="alert alert-success" role="alert">
                                  <h4>You have no invoice on your account.</h4>
                              </div>

                          </div>
                      </div>
                      <div class="col-md-5">
                          <h6 class="card-title text-center">Courses</h6>
                          <table class="table table-striped table-striped-columns">
                              <thead>
                              <tr>
                                  <th>Course Code</th>
                                  <th>Course Name</th>
                              </tr>
                              </thead>
                              <tbody>
                              {{--                              @foreach($type as $m)--}}
                              {{--                                  <tr>--}}
                              {{--                                      <td>{{ $loop->iteration }}</td>--}}
                              {{--                                      <td>{{ $m->name }}</td>--}}
                              {{--                                      <td>{{ $m->description}}</td>--}}
                              {{--                                      <td class="text-center">--}}
                              {{--                                          <div class="list-icons">--}}
                              {{--                                              <div class="dropdown">--}}
                              {{--                                                  <a href="#" class="list-icons-item" data-toggle="dropdown">--}}
                              {{--                                                      <i class="icon-menu9"></i>--}}
                              {{--                                                  </a>--}}

                              {{--                                                  <div class="dropdown-menu dropdown-menu-left">--}}
                              {{--                                                      @if(true)--}}
                              {{--                                                          Edit--}}
                              {{--                                                          <a href="{{ route('studymodes.edit', $m->id) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>--}}
                              {{--                                                      @endif--}}
                              {{--                                                      @if(true)--}}
                              {{--                                                          Delete--}}
                              {{--                                                          <a id="{{ $m->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>--}}
                              {{--                                                          <form method="post" id="item-delete-{{ $m->id }}" action="{{ route('periodtypes.destroy', $m->id) }}" class="hidden">@csrf @method('delete')</form>--}}
                              {{--                                                      @endif--}}

                              {{--                                                  </div>--}}
                              {{--                                              </div>--}}
                              {{--                                          </div>--}}
                              {{--                                      </td>--}}
                              {{--                                  </tr>--}}
                              {{--                              @endforeach--}}
                              </tbody>
                          </table>
                      </div>
                      <div class="alert alert-warning d-flex justify-content-between bg-warning-400" role="alert">
                          <h4>Registration is not opened for your Programme.</h4>
                      </div>
                      </div>
                  </div>

              </div>
            </div>
        </div>
    </div>
@endsection
