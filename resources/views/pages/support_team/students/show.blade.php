@extends('layouts.master')
@section('page_title', 'Student Profile - ')
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
                    <h3 class="mt-3">{{ $data['names'] }}</h3>
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
                            <a href="#account-info" class="nav-link active"
                               data-toggle="tab">{{ 'Account Details' }}</a>
                        </li>
                        <li class="nav-item">
                            <a href="#profile-info" class="nav-link" data-toggle="tab">{{ 'Profile Details' }}</a>
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
                                    {{--                                <td>{{ $data['intake'] }}</td>--}}
                                </tr>

                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane fade show" id="profile-info">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td class="font-weight-bold">Gender</td>
                                    <td>{{ $data['gender'] }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Email</td>
                                    <td>{{ $data['email']}}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">NRC</td>
                                    <td>{{ $data['nrc']}}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Date of Birth</td>
                                    <td>{{ $data['personal_information']['dob'] }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Marital Status</td>
                                    <td>{{ $data['personal_information']['maritalStatus'] }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Mobile</td>
                                    <td>{{ $data['personal_information']['mobile'] }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Street</td>
                                    <td>{{ $data['personal_information']['street'] }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Province</td>
                                    <td>{{ $data['personal_information']['province'] }}</td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-collapsed">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Enrollments Information</h6>
                    {!! Qs::getPanelOptions() !!}
                </div>

                <div class="card-body">

                    <ul class="nav nav-tabs nav-tabs-highlight">
                        @foreach($enrollments as $en)
                            <li class="nav-item">
                                <a href="#info{{ $en['key'] }}" class="nav-link admin-link">{{ $en['code'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="tab-content">
                        @foreach($enrollments as $en)
                            <div class="tab-pane fade show admin-pane" id="info{{ $en['key'] }}">
                                <table class="table datatable-button-html5-columns">
                                    <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Instructor</th>
                                        <th>Score</th>
                                        <th>Grade</th>
                                        <th>Edit</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($en['classes'] as $class)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $class['course_code'] }}</td>
                                            <td>{{ $class['course_name'] }}</td>
                                            <td>{{ $class['instructor'] }}</td>
                                            <td>{{ $class['total_score'] }}</td>
                                            <td>{{ $class['grade'] }}</td>
                                            <td>{{ $class['grade'] }}</td>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card card-collapsed">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Accounting Information</h6>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item"><a href="#summary" class="nav-link active">Summary</a></li>
                        <li class="nav-item"><a href="#quotations" class="nav-link">Quotations</a></li>
                        <li class="nav-item"><a href="#invoices" class="nav-link">Invoices</a></li>
                        <li class="nav-item"><a href="#receipts" class="nav-link">Receipts</a></li>
                        <li class="nav-item"><a href="#credit-notes" class="nav-link">Credit Notes</a></li>
                        <li class="nav-item"><a href="#non-cash-payments" class="nav-link">Non Cash Payments</a></li>
                        <li class="nav-item"><a href="#statement" class="nav-link">Statement of Account</a></li>
                    </ul>

                    <div class="tab-content">
                        {{--Basic Info--}}
                        <div class="tab-pane fade show active" id="summary">

                        </div>
                        <div class="tab-pane fade show" id="quotations">
                            <table class="table datatable-button-html5-columns">
                                <thead>
                                <tr>
                                    <th>Quotation #</th>
                                    <th>Names</th>
                                    <th>Date</th>
                                    <th>Grand Total</th>
                                    <th>Operation</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($accounting['quotations'] as $quot)
                                    <tr>
                                        <td>{{ $quot['id'] }}</td>
                                        <td>{{ $quot['names'] }}</td>
                                        <td>{{ $quot['date'] }}</td>
                                        <td>{{ $quot['total'] }}</td>
                                        <td></td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade show" id="invoices">
                            <table class="table datatable-button-html5-columns">
                                <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Academic Period</th>
                                    <th>Raised By</th>
                                    <th>Date</th>
                                    <th>Grand Total</th>
                                    <th>Operation</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($accounting['invoices'] as $inv)
                                    <tr>
                                        <td>{{ $inv['id'] }}</td>
                                        <td>{{ $inv['academicPeriod'] }}</td>
                                        <td>{{ $inv['raisedby'] }}</td>
                                        <td>{{ $inv['date'] }}</td>
                                        <td>{{ $inv['total'] }}</td>
                                        <td></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade show" id="receipts">
                            <table class="table datatable-button-html5-columns">
                                <thead>
                                <tr>
                                    <th>Receipt #</th>
                                    <th>Payment Method</th>
                                    <th>Collected By</th>
                                    <th>Date</th>
                                    <th>Grand Total</th>
                                    <th>Operation</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($accounting['receipts'] as $recei)
                                    <tr>
                                        <td>{{ $recei['id'] }}</td>
                                        <td>{{ $recei['payment_method'] }}</td>
                                        <td>{{ $recei['collectedBy'] }}</td>
                                        <td>{{ $recei['date'] }}</td>
                                        <td>ZMW {{ $recei['ammount_paid'] }}</td>
                                        <td></td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade show" id="credit-notes">
                            <table class="table datatable-button-html5-columns">
                                <thead>
                                <tr>
                                    <th>Credit Note #</th>
                                    <th>Invoice No#</th>
                                    <th>Names</th>
                                    <th>Student ID</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Issued By</th>
                                    <th>Authorized By</th>
                                    <th>operation</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($accounting['credit_notes'] as $cd)
                                    <tr>
                                        <td>{{ $cd['id'] }}</td>
                                        <td>{{ $cd['invoice_id'] }}</td>
                                        <td>{{ $cd['name'] }}</td>
                                        <td>{{ $cd['studentid'] }}</td>
                                        <td>ZMW {{ $cd['total'] }}</td>
                                        <td>{{ $cd['status'] }}</td>
                                        <td>{{ $cd['issued_by'] }}</td>
                                        <td>{{ $cd['authorized_by'] }}</td>
                                        <td>{{ $cd['status'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade show" id="non-cash-payments">
                            <table class="table datatable-button-html5-columns">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Invoice No.</th>
                                    <th>Amount</th>
                                    <th>Discount</th>
                                    <th>Comment</th>
                                    <th>Status</th>
                                    <th>Raised By</th>
                                    <th>Processed By</th>
                                    <th>Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                {{--                                @foreach($accounting['nonCashPayments'] as $ncp)--}}
                                {{--                                    <tr>--}}
                                {{--                                        <td>{{ $ncp['id'] }}</td>--}}
                                {{--                                        <td>{{ $cd['invoice_id'] }}</td>--}}
                                {{--                                        <td>{{ $cd['name'] }}</td>--}}
                                {{--                                        <td>{{ $cd['student_id'] }}</td>--}}
                                {{--                                        <td>ZMW {{ $cd['total'] }}</td>--}}
                                {{--                                        <td>{{ $cd['status'] }}</td>--}}
                                {{--                                        <td>{{ $cd['issued_by'] }}</td>--}}
                                {{--                                        <td>{{ $cd['authorized_by'] }}</td>--}}
                                {{--                                        <td>{{ $cd['status'] }}</td>--}}
                                {{--                                    </tr>--}}
                                {{--                                @endforeach--}}
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade show" id="statement">
                            <table class="table datatable-button-html5-columns">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Reference #</th>
                                    <th>Description</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($accounting['statement'] as $stmt)
                                    <tr>
                                        <td>{{ $stmt['date'] }}</td>
                                        <td>{{ $stmt['reference'] }}</td>
                                        <th>{{ $stmt['description'] }}</th>
                                        <td>{{ $stmt['debit'] }}</td>
                                        <td>{{ $stmt['credit'] }}</td>
                                        <td>ZMW {{ $stmt['balance'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
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



                            {{--                            <table class="table table-striped table-striped-columns">--}}
                            {{--                                <thead>--}}
                            {{--                                <tr>--}}
                            {{--                                    <th>Name</th>--}}
                            {{--                                    <th>Amount (K)</th>--}}
                            {{--                                </tr>--}}
                            {{--                                </thead>--}}
                            {{--                                <tbody>--}}
                            {{--                                @foreach($type as $m)--}}
                            {{--                                    <tr>--}}
                            {{--                                        <td>{{ $loop->iteration }}</td>--}}
                            {{--                                        <td>{{ $m->name }}</td>--}}
                            {{--                                        <td>{{ $m->description}}</td>--}}
                            {{--                                    </tr>--}}
                            {{--                                @endforeach--}}
                            {{--                                </tbody>--}}
                            {{--                            </table>--}}
                            <div>
                                <div class="alert alert-success" role="alert">
                                    <h4>You have no invoice on your account.</h4>
                                </div>

                            </div>
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

                            {{--                            <table class="table table-striped table-striped-columns">--}}
                            {{--                                <thead>--}}
                            {{--                                <tr>--}}
                            {{--                                    <th>Course Code</th>--}}
                            {{--                                    <th>Course Name</th>--}}
                            {{--                                    <th>Status</th>--}}
                            {{--                                </tr>--}}
                            {{--                                </thead>--}}
                            {{--                                <tbody>--}}
                            {{--                                @foreach($type as $m)--}}
                            {{--                                    <tr>--}}
                            {{--                                        <td>{{ $loop->iteration }}</td>--}}
                            {{--                                        <td>{{ $m->name }}</td>--}}
                            {{--                                        <td>{{ $m->description}}</td>--}}
                            {{--                                    </tr>--}}
                            {{--                                @endforeach--}}
                            {{--                                </tbody>--}}
                            {{--                            </table>--}}
                        </div>
                        <div class="alert alert-warning d-flex justify-content-between bg-warning-400" role="alert">
                            <h4>Registration is not opened for your Programme.</h4>
                        </div>
                    </div>
                </div>

            </div>
        </div>

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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const navLinks = document.querySelectorAll('.admin-link');
            const tabContents = document.querySelectorAll('.admin-pane');

            navLinks.forEach(link => {
                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    navLinks.forEach(navLink => navLink.classList.remove('active'));
                    tabContents.forEach(tabContent => tabContent.classList.remove('active', 'show'));

                    this.classList.add('active');
                    const target = this.getAttribute('href');
                    document.querySelector(target).classList.add('active', 'show');
                });
            });
        });
    </script>
@endsection
