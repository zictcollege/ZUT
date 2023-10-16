@extends('layouts.master')
@section('page_title', 'Manage Class Assessment')
@section('content')
    @php
        use App\Helpers\Qs;
    @endphp
    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Manage Class Assessment Exams Manager</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight" role="tablist" id="myTabs">
                <li class="nav-item">
                    <a href="#new-class-assessment" class="nav-link active" data-toggle="tab">Assign Assessment</a>
                </li>
                <li class="nav-item dropdown">
                    <a href="#tab" class="nav-link dropdown-toggle" data-toggle="dropdown">Manage class Assessment</a>
                    <div class="dropdown-menu dropdown-menu-right">
                        @foreach($academicPeriodsArray as $academicPeriod)
                            <a href="#ut-{{ Qs::hash($academicPeriod['academic_period_id']) }}" class="dropdown-item"
                               data-toggle="tab">{{ $academicPeriod['academic_period_code'] }}s</a>
                        @endforeach
                    </div>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="new-class-assessment">
                    <div class="row">
                        <div class="col-md-6">
                            <form class="ajax-store" method="post" action="{{ route('classAssessments.store')  }}">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold" for="nal_id">Academic
                                        Period: <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select onchange="getAcClassesPD(this.value)" data-placeholder="Choose..."
                                                name="academic" required id="nal_id" class="select-search form-control">
                                            <option value=""></option>
                                            @foreach($open as $nal)
                                                <option  value="{{ $nal->id }}">{{ $nal->code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="classID" class="col-lg-3 col-form-label font-weight-semibold">Class:
                                        <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select data-placeholder="Choose..." required name="classID" id="classID"
                                                class=" select-search form-control">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="assesmentID" class="col-lg-3 col-form-label font-weight-semibold">Assessment
                                        Type: <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select data-placeholder="Choose..." required name="assesmentID"
                                                id="assesmentID" class=" select-search form-control">
                                            <option value=""></option>
                                            @foreach($assess as $a)
                                                <option {{ (old('id') == $a->id ? 'selected' : '') }} value="{{ $a->id }}">{{ $a->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Total (%)<span
                                                class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="total" value="{{ old('total') }}" required type="number" min="1"
                                               class="form-control" placeholder="Total">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Due Date<span
                                                class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input autocomplete="off" name="end_date" value="{{ old('end_date') }}"
                                               type="text" class="form-control date-pick" placeholder="ADue Date">
                                    </div>
                                </div>


                                <div class="text-right">
                                    <button id="ajax-btn" type="submit" class="btn btn-primary">Submit form <i
                                                class="icon-paperplane ml-2"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                @foreach($academicPeriodsArray as $academicPeriod)
                    <div class="tab-pane fade reloadThisDiv"
                         id="ut-{{ Qs::hash($academicPeriod['academic_period_id']) }}">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Class Name</th>
                                <th>Class code</th>
                                <th>Type</th>
                                <th>Total</th>
                                <th>Due Date</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($academicPeriod['class_assessments'] as $classAssessment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $classAssessment['course_name'] }}</td>
                                    <td>{{ $classAssessment['course_code'] }}</td>
                                    <td>{{ $classAssessment['assessment_type_name'] }}</td>
                                    <td>
                                    <span class="display-mode"
                                          id="display-mode{{ Qs::hash($classAssessment['class_assessment_id']) }}">{{ $classAssessment['total'] }}</span>
                                        <input type="text" class="edit-mode form-control"
                                               id="class{{ Qs::hash($classAssessment['class_assessment_id']) }}"
                                               value="{{ $classAssessment['total'] }}" style="display: none;"
                                               onchange="updateExamResults('{{Qs::hash($classAssessment['class_assessment_id'])}}')">
                                    </td>
                                    <td>
                                    <span class="display-mode"
                                          id="display-mode-enddate{{ Qs::hash($classAssessment['class_assessment_id']) }}">{{ $classAssessment['end_date'] }}</span>
                                        <input type="text" class="edit-mode form-control  date-pick"
                                               id="enddate{{ Qs::hash($classAssessment['class_assessment_id']) }}"
                                               value="{{ $classAssessment['end_date'] }}" style="display: none;"
                                               onchange="updateExamResults('{{Qs::hash($classAssessment['class_assessment_id'])}}')">
                                    </td>
                                    <td class="text-center">
                                        <div class="list-icons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-left">

{{--                                                    <a href="{{ route('classAssessments.show', Qs::hash($classAssessment['class_assessment_id'])) }}"--}}
{{--                                                       class="dropdown-item"><i class="icon-eye"></i> View Profile</a>--}}

                                                    <a href="#" class="dropdown-item edit-total-link"><i
                                                                class="icon-pencil"></i> Edit</a>
                                                    @if(Qs::userIsSuperAdmin())
                                                        <a id="{{ Qs::hash($classAssessment['class_assessment_id']) }}"
                                                           onclick="confirmDelete(this.id)" href="#"
                                                           class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                        <form method="post"
                                                              id="item-delete-{{ Qs::hash($classAssessment['class_assessment_id']) }}"
                                                              action="{{ route('classAssessments.destroy', Qs::hash($classAssessment['class_assessment_id'])) }}"
                                                              class="hidden">@csrf @method('delete')</form>
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
