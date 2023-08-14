@extends('layouts.master')
@section('page_title', 'Manage Users')
@section('content')
    @php
        use App\Helpers\Qs;
    @endphp
    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Manage Users</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#new-user" class="nav-link active" data-toggle="tab">Create New User</a></li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Manage Users</a>
                    <div class="dropdown-menu dropdown-menu-right">
                        @foreach($user_types as $ut)
                            <a href="#ut-{{ Qs::hash($ut->id) }}" class="dropdown-item" data-toggle="tab">{{ $ut->name }}s</a>
                        @endforeach
                    </div>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="new-user">
                    <form method="post" enctype="multipart/form-data" class="wizard-form steps-validation ajax-store" action="{{ route('users.store') }}" data-fouc>
                        @csrf
                    <h6>Personal Data</h6>
                        <fieldset>
                            <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>First Name: <span class="text-danger">*</span></label>
                                            <input value="{{ old('first_name') }}" required type="text" name="first_name" placeholder="First Name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Middle Name: </label>
                                            <input value="{{ old('middle_name') }}" type="text" name="middle_name" placeholder="Middle Name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Last Name: <span class="text-danger">*</span></label>
                                            <input value="{{ old('last_name') }}" required type="text" name="last_name" placeholder="Last Name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Email Address: <span class="text-danger">*</span></label>
                                            <input value="{{ old('email') }}"  required class="form-control" placeholder="Email Address" name="email" type="text" >
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>NRC: <span class="text-danger">*</span></label>
                                            <input type="nrc" value="{{ old('nrc') }}" required name="nrc" class="form-control" placeholder="NRC Number xxxxxx/xx/x">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="gender">Gender: <span class="text-danger">*</span></label>
                                            <select class="select form-control" required id="gender" name="gender" data-fouc data-placeholder="Choose..">
                                                <option value=""></option>
                                                <option {{ (old('gender') == 'Male') ? 'selected' : '' }} value="Male">Male</option>
                                                <option {{ (old('gender') == 'Female') ? 'selected' : '' }} value="Female">Female</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Phone: <span class="text-danger">*</span></label>
                                            <input value="{{ old('phone') }}" required type="text" name="mobile" class="form-control" placeholder="" >
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Telephone:</label>
                                            <input value="{{ old('telephone') }}" type="text" name="telephone" class="form-control" placeholder="" >
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Date of Birth: <span class="text-danger">*</span></label>
                                            <input name="dob" value="{{ old('dob') }}" required type="text" class="form-control date-pick" placeholder="Select Date...">

                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="nationality">Nationality: <span class="text-danger">*</span></label>
                                            <select onchange="getStates(this.value)" required data-placeholder="Choose..." name="nationality" id="nationality" class="select-search form-control">
                                                <option value=""></option>
                                                @foreach($nationals as $nal)
                                                    <option {{ (old('nal_id') == $nal->id ? 'selected' : '') }} value="{{ $nal->id }}">{{ $nal->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="state_id">State: <span class="text-danger">*</span></label>
                                        <select onchange="getTowns(this.value)" required data-placeholder="Choose.." class="select-search form-control" name="province_state" id="state_id">
                                            <option value=""></option>
                                            {{--                                @foreach($states as $st)--}}
                                            {{--                                    <option {{ (old('state_id') == $st->id ? 'selected' : '') }} value="{{ $st->id }}">{{ $st->name }}</option>--}}
                                            {{--                                @endforeach--}}
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="lga_id">Town: <span class="text-danger">*</span></label>
                                        <select data-placeholder="Select State First" required class="select-search form-control" name="town_city" id="lga_id">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Passport Number: </label>
                                            <input type="text" value="{{ old('passport_number') }}" name="passport" class="form-control" placeholder="Passport Number">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="marital_status">Marital Status: <span class="text-danger">*</span></label>
                                            <select class="select form-control" required id="marital_status" name="marital_status" data-fouc data-placeholder="Choose..">
                                                <option value=""></option>
                                                <option {{ (old('gender') == 'Single') ? 'selected' : '' }} value="Male">Single</option>
                                                <option {{ (old('gender') == 'Married') ? 'selected' : '' }} value="Female">Married</option>
                                                <option {{ (old('gender') == 'Divorced') ? 'selected' : '' }} value="Female">Divorced</option>
                                                <option {{ (old('gender') == 'Widowed') ? 'selected' : '' }} value="Female">Widowed</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Street Main: <span class="text-danger">*</span></label>
                                            <input value="{{ old('street_main') }}" required type="text" name="street_main" class="form-control" placeholder="">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Postal code:</label>
                                            <input value="{{ old('post_code') }}" type="number" name="post_code" class="form-control" placeholder="">
                                        </div>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="d-block">Upload Passport Photo:</label>
                                            <input value="{{ old('photo') }}" accept="image/*" type="file" name="photo" class="form-input-styled" >
                                            <span class="form-text text-muted">Accepted Images: jpeg, png. Max file size 2Mb</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="user_type"> Select User: <span class="text-danger">*</span></label>
                                            <select required data-placeholder="Select User" class="form-control select" name="user_type" id="user_type">
                                                @foreach($user_types as $ut)
{{--                                                    <option value="{{ Qs::hash($ut->id) }}">{{ $ut->name }}</option>--}}
                                                    <option value="{{ $ut->title }}">{{ $ut->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                        </fieldset>
                    </form>
                </div>

                @foreach($user_types as $ut)
                    <div class="tab-pane fade" id="ut-{{Qs::hash($ut->id)}}">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users->where('user_type', $ut->title) as $u)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $u->photo }}" alt="photo"></td>
                                    <td>{{ $u->first_name.' '.$u->middle_name.' '.$u->last_name }}</td>
                                    <td>{{ $u->email }}</td>
                                    <td>{{ $u->personalinfo->mobile }}</td>
                                    <td>{{ $u->email }}</td>
                                    <td class="text-center">
                                        <div class="list-icons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-left">
                                                    {{--View Profile--}}
                                                    <a href="{{ route('users.show', Qs::hash($u->id)) }}" class="dropdown-item"><i class="icon-eye"></i> View Profile</a>
                                                    {{--Edit--}}
                                                    <a href="{{ route('users.edit', Qs::hash($u->id)) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                                @if(Qs::userIsSuperAdmin())

{{--                                                        <a href="{{ route('users.reset_pass', Qs::hash($u->id)) }}" class="dropdown-item"><i class="icon-lock"></i> Reset password</a>--}}
                                                        {{--Delete--}}
                                                        <a id="{{ Qs::hash($u->id) }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                        <form method="post" id="item-delete-{{ Qs::hash($u->id) }}" action="{{ route('users.destroy', Qs::hash($u->id)) }}" class="hidden">@csrf @method('delete')</form>
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
                @endforeach

            </div>
        </div>
    </div>

    {{--Student List Ends--}}

@endsection
