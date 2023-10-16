@extends('layouts.master')
@section('page_title', 'Manage Class Assessment')
@section('content')
    @php
        use App\Helpers\Qs;
    @endphp

        <div class="card">
            <div class="card-header header-elements-inline">
                <h6 class="card-title">Manage Assessment</h6>
                {!! Qs::getPanelOptions() !!}
            </div>

            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-highlight">
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Post Results</a>
                        <div class="dropdown-menu dropdown-menu-right">
                            @foreach($academicPeriodsArray as $academicPeriod)
                                <a href="#ut-{{ Qs::hash($academicPeriod['academic_period_id']) }}" class="dropdown-item" data-toggle="tab">{{ $academicPeriod['academic_period_code'] }}s</a>
                            @endforeach
                        </div>
                    </li>
                    <li class="nav-item"><a href="#Upload-results" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i>Post results</a></li>
                </ul>

                <div class="tab-content">
                    @foreach($academicPeriodsArray as $academicPeriod)
                        <div class="tab-pane fade" id="ut-{{ Qs::hash($academicPeriod['academic_period_id']) }}">
                            <table class="table datatable-button-html5-columns">
                                <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Class Name</th>
                                    <th>Type </th>
                                    <th>Total </th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($academicPeriod['class_assessments'] as $classAssessment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $classAssessment['course_name'] }}</td>
                                        <td>{{ $classAssessment['assessment_type_name'] }}</td>
                                        <td>
                                            <span class="display-mode" id="display-mode{{ Qs::hash($classAssessment['class_assessment_id']) }}">{{ $classAssessment['total'] }}</span>
                                            <input type="text" class="edit-mode" id="class{{ Qs::hash($classAssessment['class_assessment_id']) }}" value="{{ $classAssessment['total'] }}" style="display: none;" onchange="updateExamResults('{{Qs::hash($classAssessment['class_assessment_id'])}}')">
                                        </td>


                                        <td class="text-center">
                                            <div class="list-icons">
                                                <div class="dropdown">
                                                    <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                        <i class="icon-menu9"></i>
                                                    </a>

                                                    <div class="dropdown-menu dropdown-menu-left">

                                                        <a href="{{ route('classAssessments.show', Qs::hash($classAssessment['class_assessment_id'])) }}" class="dropdown-item"><i class="icon-eye"></i> View Profile</a>

                                                        <a href="#" class="dropdown-item edit-total-link"><i class="icon-pencil"></i> Edit</a>
                                                        @if(Qs::userIsSuperAdmin())
                                                            <a id="{{ Qs::hash($classAssessment['class_assessment_id']) }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                            <form method="post" id="item-delete-{{ Qs::hash($classAssessment['class_assessment_id']) }}" action="{{ route('classAssessments.destroy', Qs::hash($classAssessment['class_assessment_id'])) }}" class="hidden">@csrf @method('delete')</form>
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
                        <div class="tab-pane fade show" id="Upload-results">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            Import CSV or Excel File
                                        </div>
                                        <div class="card-body">
                                            @if(session('success'))
                                                <div class="alert alert-success">
                                                    {{ session('success') }}
                                                </div>
                                            @endif

                                            @if(empty($data))
                                                <!-- Import Form -->
                                                <form method="POST" action="{{ route('import.process') }}"
                                                      enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label font-weight-semibold"
                                                               for="nal_id">Academic Period: <span class="text-danger">*</span></label>
                                                        <div class="col-lg-9">
                                                            <select onchange="getRunningPrograms(this.value)"
                                                                    data-placeholder="Choose..." name="academic" required
                                                                    id="nal_id" class="select-search form-control">
                                                                <option value="">Choose</option>
                                                                @foreach($open as $ac)
                                                                    <option {{ (old('nal_id') == $ac->id ? 'selected' : '') }} value="{{ Qs::hash($ac->id) }}">{{ $ac->code }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="classID"
                                                               class="col-lg-3 col-form-label font-weight-semibold">Class: <span
                                                                    class="text-danger">*</span></label>
                                                        <div class="col-lg-9">
                                                            <select data-placeholder="Choose..." required name="programID"
                                                                    id="classID" class=" select-search form-control">
                                                                <option value="">Choose</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="classID"
                                                               class="col-lg-3 col-form-label font-weight-semibold">Choose File
                                                            <span class="text-danger">*</span></label>
                                                        <div class="col-lg-9">
                                                            <input type="file" class="form-control-file" id="file" name="file"
                                                                   required>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Upload and Preview</button>
                                                </form>
                                            @else
                                                <!-- Data Preview Table -->
                                                <h2>Results Preview</h2>
                                                <table class="table table-bordered table-hover datatable-button-html5-columns">
                                                    <thead>
                                                    <tr>
                                                        @foreach($data[0] as $column => $value)
                                                            <th>{{ $column }}</th>
                                                        @endforeach
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($data as $row)
                                                        <tr>
                                                            @foreach($row as $value)
                                                                <td>{{ $value }}</td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>

                                                <!-- Import Button -->
                                                <div class="row col mb-4 mt-3">
                                                    <form method="POST" action="{{ route('import.process') }}"
                                                          enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="form-group">
                                                            <input type="file" class="form-control-file" id="file" name="file"
                                                                   required>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Import Data</button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>

        {{--Class List Ends--}}

    @endsection



    {{--Student List Ends--}}

@endsection
