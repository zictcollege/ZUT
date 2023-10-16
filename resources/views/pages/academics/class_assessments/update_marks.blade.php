@extends('layouts.master')
@section('page_title', 'Update Student Class')
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
                    <div class="tab-pane fade">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Class Name</th>
                                <th>Type</th>
                                <th>Total</th>
                                <th>Due Date</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
{{--                            @foreach($academicPeriod['class_assessments'] as $classAssessment)--}}
{{--                                <tr>--}}
{{--                                    <td>{{ $loop->iteration }}</td>--}}
{{--                                    <td>{{ $classAssessment['course_name'] }}</td>--}}
{{--                                    <td>{{ $classAssessment['assessment_type_name'] }}</td>--}}
{{--                                    <td>--}}
{{--                                    <span class="display-mode"--}}
{{--                                          id="display-mode{{ Qs::hash($classAssessment['class_assessment_id']) }}">{{ $classAssessment['total'] }}</span>--}}
{{--                                        <input type="text" class="edit-mode form-control"--}}
{{--                                               id="class{{ Qs::hash($classAssessment['class_assessment_id']) }}"--}}
{{--                                               value="{{ $classAssessment['total'] }}" style="display: none;"--}}
{{--                                               onchange="updateExamResults('{{Qs::hash($classAssessment['class_assessment_id'])}}')">--}}
{{--                                    </td>--}}
{{--                                    <td>--}}
{{--                                    <span class="display-mode"--}}
{{--                                          id="display-mode-enddate{{ Qs::hash($classAssessment['class_assessment_id']) }}">{{ $classAssessment['end_date'] }}</span>--}}
{{--                                        <input type="text" class="edit-mode form-control date-pick"--}}
{{--                                               id="enddate{{ Qs::hash($classAssessment['class_assessment_id']) }}"--}}
{{--                                               value="{{ $classAssessment['end_date'] }}" style="display: none;"--}}
{{--                                               onchange="updateExamResults('{{Qs::hash($classAssessment['class_assessment_id'])}}')">--}}
{{--                                    </td>--}}
{{--                                    <td class="text-center">--}}
{{--                                        <div class="list-icons">--}}
{{--                                            <div class="dropdown">--}}
{{--                                                <a href="#" class="list-icons-item" data-toggle="dropdown">--}}
{{--                                                    <i class="icon-menu9"></i>--}}
{{--                                                </a>--}}

{{--                                                <div class="dropdown-menu dropdown-menu-left">--}}

{{--                                                    <a href="{{ route('classAssessments.show', Qs::hash($classAssessment['class_assessment_id'])) }}"--}}
{{--                                                       class="dropdown-item"><i class="icon-eye"></i> View Profile</a>--}}

{{--                                                    <a href="#" class="dropdown-item edit-total-link"><i--}}
{{--                                                                class="icon-pencil"></i> Edit</a>--}}
{{--                                                    @if(Qs::userIsSuperAdmin())--}}
{{--                                                        <a id="{{ Qs::hash($classAssessment['class_assessment_id']) }}"--}}
{{--                                                           onclick="confirmDelete(this.id)" href="#"--}}
{{--                                                           class="dropdown-item"><i class="icon-trash"></i> Delete</a>--}}
{{--                                                        <form method="post"--}}
{{--                                                              id="item-delete-{{ Qs::hash($classAssessment['class_assessment_id']) }}"--}}
{{--                                                              action="{{ route('classAssessments.destroy', Qs::hash($classAssessment['class_assessment_id'])) }}"--}}
{{--                                                              class="hidden">@csrf @method('delete')</form>--}}
{{--                                                    @endif--}}

{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </td>--}}
{{--                                </tr>--}}
{{--                            @endforeach--}}
                            </tbody>
                        </table>
                    </div>
        </div>
    </div>

@endsection
