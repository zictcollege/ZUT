@extends('layouts.master')
@section('page_title', 'Edit Academic Period')
@section('content')
    @php
        use App\Helpers\Qs;
    @endphp
    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Edit an Academic Period</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="col-md-8">
                <form class="ajax-update" method="post" action="{{ route('acupdate', Qs::hash($period->id) ) }}">
                    @csrf @method('PUT')
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label font-weight-semibold">Code <span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input name="code" value="{{ $period->code }}" required type="text" class="form-control" placeholder="code">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="my_class_id" class="col-lg-3 col-form-label font-weight-semibold">Start Date <span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input autocomplete="off" name="acStartDate" value="{{ $period->acStartDate }}" type="text" class="form-control date-pick" placeholder="Academic Start Date">

                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="exam_id" class="col-lg-3 col-form-label font-weight-semibold">End date <span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input autocomplete="off" name="acEndDate" value="{{ $period->acEndDate }}" type="text" class="form-control date-pick" placeholder="Select Date...">

                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="exam_id" class="col-lg-3 col-form-label font-weight-semibold">Registration End date <span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input autocomplete="off" name="registrationDate" value="{{ $period->registrationDate }}" type="text" class="form-control date-pick" placeholder="Select Date...">

                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="exam_id" class="col-lg-3 col-form-label font-weight-semibold">Late Registration End date <span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input autocomplete="off" name="lateRegistrationDate" value="{{ $period->lateRegistrationDate }}" type="text" class="form-control date-pick" placeholder="Select Date...">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label font-weight-semibold">Registration Threshold <span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input name="registrationThreshold" value="{{ $period->registrationThreshold }}" required type="text" class="form-control" placeholder="%">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label font-weight-semibold">View Results Threshold <span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input name="resultsThreshold" value="{{ $period->resultsThreshold }}" required type="text" class="form-control" placeholder="%">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label font-weight-semibold">Download Exam Slip Threshold <span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input name="examSlipThreshold" value="{{ $period->examSlipThreshold }}" required type="text" class="form-control" placeholder="%">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="exam_id" class="col-lg-3 col-form-label font-weight-semibold">Period ID <span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input autocomplete="off" name="periodID" value="{{ $period->periodID }}" type="text" class="form-control" placeholder="Select Date...">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="study-mode" class="col-lg-3 col-form-label font-weight-semibold">Select Study Mode <span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <select required data-placeholder="Select Class" class="form-control select" name="studyModeIDAllowed" id="study-mode">
                                <option value="{{$period->studyModeIDAllowed}}">{{$period->studyMode->name}}</option>
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
                                <option value="{{$period->type }}">{{$period->periodType->name}}</option>
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
    </div>

    {{--TimeTable Edit Ends--}}

@endsection
