@extends('layouts.master')
@section('page_title', 'Admit Student')
@section('content')
    @php
        use App\Helpers\Qs;
    @endphp
        <div class="card">
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title">Please fill The form Below To Admit A New Student</h6>

                {!! Qs::getPanelOptions() !!}
            </div>

            <form id="ajax-reg" method="post" enctype="multipart/form-data" class="wizard-form steps-validation" action="{{ route('students.store') }}" data-fouc>
               @csrf
                <h6>Personal data</h6>
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
                                    <option {{ (old('marital_status') == 'Single') ? 'selected' : '' }} value="Single">Single</option>
                                    <option {{ (old('marital_status') == 'Married') ? 'selected' : '' }} value="Married">Married</option>
                                    <option {{ (old('marital_status') == 'Divorced') ? 'selected' : '' }} value="Divorced">Divorced</option>
                                    <option {{ (old('marital_status') == 'Widowed') ? 'selected' : '' }} value="Widowed">Widowed</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Street Main: <span class="text-danger">*</span></label>
                                <input value="{{ old('street_main') }}" required type="text" name="street_main" class="form-control" placeholder="" >
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Postal code:</label>
                                <input value="{{ old('post_code') }}" type="number" name="post_code" class="form-control" placeholder="" >
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="d-block">Upload Passport Photo:</label>
                                <input value="{{ old('photo') }}" accept="image/*" type="file" name="photo" class="form-input-styled" data-fouc>
                                <span class="form-text text-muted">Accepted Images: jpeg, png. Max file size 2Mb</span>
                            </div>
                        </div>
                    </div>

                </fieldset>

                <h6>Next of kin</h6>
                <fieldset>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Full Name: <span class="text-danger">*</span></label>
                                <input value="{{ old('full_name') }}" required type="text" name="nk_full_name" placeholder="Full Name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Email: <span class="text-danger">*</span></label>
                                <input value="{{ old('email') }}" required type="text" name="nk_email" placeholder="Email" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Phone: <span class="text-danger">*</span></label>
                                <input value="{{ old('phone') }}" type="text" required name="nk_phone" placeholder="Phone" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Address: <span class="text-danger">*</span></label>
                                <input value="{{ old('address') }}" required class="form-control" placeholder="Address" name="nkaddress" type="text">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tel: <span class="text-danger">*</span></label>
                                <input type="text" value="{{ old('tel') }}" required name="nktel" class="form-control" placeholder="tel">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="gender">Relationship: <span class="text-danger">*</span></label>
                                <select class="select form-control" required id="nk_relationship" name="nk_relationship" data-fouc data-placeholder="Choose..">
                                    <option value=""></option>
                                    <option {{ (old('gender') == 'Guardian') ? 'selected' : '' }} value="Guardian">Guardian</option>
                                    <option {{ (old('gender') == 'Sibling') ? 'selected' : '' }} value="Sibling">Sibling</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="nal_id">Nationality: <span class="text-danger">*</span></label>
                                <select onchange="getStatesN(this.value)" required data-placeholder="Choose..." name="nk_nal_id" id="nal_id" class="select-search form-control">
                                    <option value=""></option>
                                    @foreach($nationals as $nal)
                                        <option {{ (old('nal_id') == $nal->id ? 'selected' : '') }} value="{{ $nal->id }}">{{ $nal->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="state_id">State: <span class="text-danger">*</span></label>
                            <select onchange="getTownsN(this.value)" required data-placeholder="Choose.." class="select-search form-control" name="nk_state_id" id="state_idn">
                                <option value=""></option>
                                {{--                                @foreach($states as $st)--}}
                                {{--                                    <option {{ (old('state_id') == $st->id ? 'selected' : '') }} value="{{ $st->id }}">{{ $st->name }}</option>--}}
                                {{--                                @endforeach--}}
                            </select>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <label for="lga_id">Town: <span class="text-danger">*</span></label>
                            <select data-placeholder="Select State First lga_id" required class="select-search form-control" name="nk_town_id" id="lga_idn">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>

                </fieldset>
                <h6>Academic Data</h6>
                <fieldset>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="qualification_id">Qualification: <span class="text-danger">*</span></label>
                                <select onchange="getPrograms(this.value)" data-placeholder="Choose..." required name="qualification_id" id="qualification_id" class="select-search form-control">
                                    <option value=""></option>
                                    @foreach($qualifications as $q)
                                        <option {{ (old('my_class_id') == $q->id ? 'selected' : '') }} value="{{ $q->id }}">{{ $q->name }}</option>
                                        @endforeach
                                </select>
                        </div>
                            </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="program_id">Program: <span class="text-danger">*</span></label>
                                <select onchange="getLevels(this.value)" data-placeholder="Select Class First" required name="programID" id="program_id" class="select-search form-control">
                                    <option {{ (old('section_id')) ? 'selected' : '' }} value="{{ old('section_id') }}">{{ (old('section_id')) ? 'Selected' : '' }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="intake_id">Intake: <span class="text-danger">*</span></label>
                                <select data-placeholder="Choose..." required name="intakeID" id="intakeID" class="select-search form-control">
                                    <option  value=""></option>
                                    @foreach($intake as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="study_mode_id">Study Mode: <span class="text-danger">*</span></label>
                                <select data-placeholder="Choose..." required name="studymodeID" id="study_mode_id" class="select-search form-control">
                                    <option value=""></option>
                                    @foreach($studyMode as $p)
{{--                                        <option {{ (old('my_parent_id') == Qs::hash($p->id)) ? 'selected' : '' }} value="{{ Qs::hash($p->id) }}">{{ $p->name }}</option>--}}
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="level_id">Current Level: </label>
                            <select data-placeholder="Choose..." required name="level_id" id="level_id" class="select-search form-control">
                                <option value=""></option>
{{--                                @foreach($dorms as $d)--}}
{{--                                    <option {{ (old('dorm_id') == $d->id) ? 'selected' : '' }} value="{{ $d->id }}">{{ $d->name }}</option>--}}
{{--                                    @endforeach--}}
                            </select>

                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Type:</label>
                                <select data-placeholder="Choose..." required name="typeID" id="type_id" class="select-search form-control">
                                <option value="1">Academic</option>
                                    <option value="2">Professional</option>

                                {{--                                @foreach($dorms as $d)--}}
                                {{--                                    <option {{ (old('dorm_id') == $d->id) ? 'selected' : '' }} value="{{ $d->id }}">{{ $d->name }}</option>--}}
                                {{--                                    @endforeach--}}
                                </select>
                            </div>
                        </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Payment Plan:</label>
                                    <select data-placeholder="Choose..." required name="paymentPlanID" id="paymentPlanID" class="select-search form-control">
                                        <option value="">Choose</option>
                                        @foreach($paymentPlanID as $p)
                                            {{--                                        <option {{ (old('my_parent_id') == Qs::hash($p->id)) ? 'selected' : '' }} value="{{ Qs::hash($p->id) }}">{{ $p->name }}</option>--}}
                                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                        </div>
                    </div>
                </fieldset>

            </form>
        </div>
    @endsection
