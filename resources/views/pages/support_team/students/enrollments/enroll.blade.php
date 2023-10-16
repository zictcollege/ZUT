@extends('layouts.master')
@section('page_title', 'Student Profile - '.$data['names'])
@section('content')
    @php
        use App\Helpers\Qs;
    @endphp
    <div class="row">
        <div class="col-md-12">
            <div class="card card-collapsed">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Account</h6>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item">
                            <a href="#account-info" class="nav-link active"
                               data-toggle="tab">{{ 'Account Details' }}</a>
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
                                    <td>{{ $data['progression']['currentLevelName'] }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Academic Year</td>
                                    <td>{{ $data['currentAcademicPeriodName']}}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Study Category</td>
                                    <td>{{ $data['currentModeName'] }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Status</td>
                                    <td>{{ $data['admissionStatus'] }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Semester</td>
                                    <td>
                                        @php
                                            $intakeID = date('m');
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
                                    <td>{{ $data['currentProgramName']  }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Programme Code</td>
                                    <td>{{ $data['currentProgram']['code'] }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Registration</h6>
            {!! Qs::getPanelOptions() !!}
        </div>
        @if($dat['has_registered'] === 0 && $dat['selectedAcademicPeriod'])
            <div class="row justify-content-between p-3">
                <div class="col-md-5">
                    <h6 class="card-title text-center">Invoice</h6>
                    @if ($dat['selectedAcademicPeriod']['type_int'] == 0)
                        <table class="table table-striped table-striped-columns table-hover">
                            <thead>
                            <tr>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if ($dat['progression']['progression'] == 0)
                                @if ($dat['selectedAcademicPeriod']['fees']['tuitionFee'])
                                    <tr>
                                        <td>{{ $dat['selectedAcademicPeriod']['fees']['tuitionFee']['name'] }}</td>
                                        <td>{{ $dat['selectedAcademicPeriod']['fees']['tuitionFee']['amount'] }}</td>
                                    </tr>
                                @endif
                                @if ($dat['semesterStatus'] == 0)
                                    @foreach ($dat['selectedAcademicPeriod']['fees']['otherFees'] as $fee)
                                        <tr>
                                            <td>{{ $fee['name'] }}</td>
                                            <td>{{ $fee['amount'] }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @elseif ($dat['progression']['progression'] == 1)
                                @if ($dat['selectedAcademicPeriod']['fees']['tuitionFee'])
                                    <tr>
                                        <td>{{ $dat['selectedAcademicPeriod']['fees']['tuitionFee']['name'] }}</td>
                                        <td>{{ $dat['selectedAcademicPeriod']['fees']['tuitionFee']['amount'] }}</td>
                                    </tr>
                                @endif
                                @if ($dat['semesterStatus'] == 0)
                                    @foreach ($dat['selectedAcademicPeriod']['fees']['otherFees'] as $fee)
                                        <tr>
                                            <td>{{ $fee['name'] }}</td>
                                            <td>{{ $fee['amount'] }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @elseif ($dat['progression']['progression'] == 2)
                                <tr>
                                    <td>Tuition</td>
                                    <td>{{ ($dat['selectedAcademicPeriod']['repeatFee']['amount']*$dat['resultsdata']['coursesFailedCount']) }}</td>
                                </tr>
                                @if ($dat['semesterStatus'] == 0)
                                    @foreach ($dat['selectedAcademicPeriod']['fees']['otherFees'] as $fee)
                                        <tr>
                                            <td>{{ $fee['name'] }}</td>
                                            <td>{{ $fee['amount'] }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @elseif ($dat['progression']['progression'] == 3)
                                @if ($dat['selectedAcademicPeriod']['fees']['tuitionFee'])
                                    <tr>
                                        <td>{{ $dat['selectedAcademicPeriod']['fees']['tuitionFee']['name'] }}</td>
                                        <td>{{ $dat['selectedAcademicPeriod']['fees']['tuitionFee']['amount'] }}</td>
                                    </tr>
                                @endif
                                @if ($dat['semesterStatus'] == 0)
                                    @foreach ($dat['selectedAcademicPeriod']['fees']['otherFees'] as $fee)
                                        <tr>
                                            <td>{{ $fee['name'] }}</td>
                                            <td>{{ $fee['amount'] }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endif
                            </tbody>
                        </table>
                    @endif
                </div>
                <div class="col-md-5">
                    <h6 class="card-title text-center">Courses</h6>
                    @if ($dat['selectedAcademicPeriod']['type_int'] == 0)
                        <table class="table table-striped table-striped-columns table-hover">
                            <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if ($dat['progression']['progression'] == 3)
                                <p>
                                    You will not be enrolled in some repeat classes if they are not running the
                                    academic period
                                </p>
                            @endif
                            @if ($dat['progression']['progression'] == 3)
                                @foreach ($dat['selectedAcademicPeriod']['next_classes'] as $index => $Aclass)
                                    <tr>
                                        <td>{{ $Aclass['course_code'] }}</td>
                                        <td>{{ $Aclass['course_name'] }}</td>
                                        <td>Repeat year</td>
                                    </tr>
                                @endforeach
                            @elseif ($dat['progression']['progression'] == 2)
                                @foreach ($dat['progression']['courses'] as $index => $Aclass)
                                    <tr>
                                        <td>{{ $Aclass['course_code'] }}</td>
                                        <td>{{ $Aclass['course_name'] }}</td>
                                        <td>Part Time</td>
                                    </tr>
                                @endforeach
                            @elseif (($dat['progression']['progression'] == 1 && ($dat['studyModeID'] == 1 || $dat['studyModeID'] == 3)))
                                @foreach ($dat['progression']['courses'] as $index => $Aclass)
                                    <tr>
                                        <td>{{ $Aclass['course_code'] }}</td>
                                        <td>{{ $Aclass['course_name'] }}</td>
                                        <td>Repeat</td>
                                    </tr>
                                @endforeach
                                @foreach ($dat['selectedAcademicPeriod']['next_classes'] as $index => $Aclass)
                                    <tr>
                                        <td>{{ $Aclass['course_code'] }}</td>
                                        <td>{{ $Aclass['course_name'] }}</td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            @elseif ($dat['progression']['progression'] == 0 && ($dat['studyModeID'] == 1 || $dat['studyModeID'] == 3))
                                @if (count($dat['selectedAcademicPeriod']['next_classes']) == 0)
                                    <tr>
                                        <td colspan="3">No Classes Set for the next academic period. Contact
                                            Registrar
                                        </td>
                                    </tr>
                                @endif
                                @foreach ($dat['selectedAcademicPeriod']['next_classes'] as $index => $Aclass)
                                    <tr>
                                        <td>{{ $Aclass['course_code'] }}</td>
                                        <td>{{ $Aclass['course_name'] }}</td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            @endif
                            <!-- for evening students -->
                            @if ($dat['progression']['progression'] == 1 && $dat['studyModeID'] == 2)
                                @foreach ($dat['progression']['courses'] as $index => $Aclass)
                                    <tr>
                                        <td>{{ $Aclass['course_code'] }}</td>
                                        <td>{{ $Aclass['course_name'] }}</td>
                                        <td>Repeat</td>
                                    </tr>
                                @endforeach
                            @endif
                            @if ($dat['selectedAcademicPeriod']['type_int'] == 0 && $dat['progression']['progression'] == 0 && $dat['studyModeID'] == 2)
                                @if (count($dat['selectedAcademicPeriod']['next_classes']) == 0)
                                    <tr>
                                        <td colspan="3">No Classes Set for the next academic period. Contact
                                            Registrar
                                        </td>
                                    </tr>
                                @endif
                                @foreach ($dat['selectedAcademicPeriod']['next_classes'] as $index => $Aclass)
                                    <tr>
                                        <td>{{ $Aclass['course_code'] }}</td>
                                        <td>{{ $Aclass['course_name'] }}</td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            @endif
                            @if ($dat['selectedAcademicPeriod']['type_int'] == 0 && $dat['progression']['progression'] == 1 && $dat['studyModeID'] == 2)
                                @if (count($dat['selectedAcademicPeriod']['next_classes']) == 0)
                                    <tr>
                                        <td colspan="3">No Classes Set for the next academic period. Contact
                                            Registrar
                                        </td>
                                    </tr>
                                @endif
                                @foreach ($dat['selectedAcademicPeriod']['next_classes'] as $index => $Aclass)
                                    <tr>
                                        <td>{{ $Aclass['course_code'] }}</td>
                                        <td>{{ $Aclass['course_name'] }}</td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    @endif

                    @if ($dat['selectedAcademicPeriod']['type_int'] == 1 && $proposedActivationData)
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($proposedActivationData['courses'] as $index => $Aclass)
                                <tr>
                                    <td>{{ $Aclass['code'] }}</td>
                                    <td>{{ $Aclass['name'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
            <div class="row mt-3 mb-4 justify-content-center"><button type="button" class="btn btn-primary">Register</button></div>
        @elseif(!$dat['selectedAcademicPeriod'])

            <div class="card-body">
                <div class="alert alert-warning d-flex justify-content-between bg-warning-400" role="alert">
                    <h4>Registration is not opened for your Programme.</h4>
                </div>
                <div>
                    <div class="alert alert-success" role="alert">
                        <h4>You have no invoice on your account.</h4>
                    </div>

                </div>
            </div>
        @endif
    </div>

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" id="launchModalButton">
        Launch static backdrop modal
    </button>

    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                    <div class="list-icons">
                        <a class="list-icons-item" data-action="remove"></a>
                    </div>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeModalButton" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary">Understood</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const launchModalButton = document.getElementById('launchModalButton');
            const closeModalButton = document.getElementById('closeModalButton');
            const staticBackdropModal = new bootstrap.Modal(document.getElementById('staticBackdrop'));

            launchModalButton.addEventListener('click', function () {
                staticBackdropModal.show();
            });
            closeModalButton.addEventListener('click', function () {
                staticBackdropModal.hide();
            });
        });
    </script>
@endsection
