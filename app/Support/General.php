<?php

namespace App\Support;


use App\Models\Academics\AcademicPeriods;
use App\Models\Academics\Classes;
use App\Models\Academics\CourseLevels;
use App\Models\Academics\Programs;
use App\Models\Accounting\Invoice;
use App\Models\Accounting\PaymentPlan;
use App\Models\Accounting\UserPaymentPlan;
use App\Models\Admissions\ProgramCourses;
use App\Models\Admissions\StudentRecord;
use App\Models\Admissions\UserPersonalInformation;
use App\Models\Admissions\UserProgram;
use App\Models\Admissions\UserStudyModes;
use App\Models\Applications\Booking;
use App\Models\Enrollment;
use App\Models\ExamRegistraion;
use App\Models\User;
use DB;
use Hash;
use Illuminate\Support\Carbon;
use Request;

class General
{
    use Accounting;

    public function user($id)
    {
        $user = self::jsondata($id);
        return $user;
    }

    public static function jsondata($id)
    {

        $user = User::find($id);


        if ($user->personalinfo) {
            $userPersonalDatails = [

                'dob'            => $user->personalinfo->dob,
                'street'         => $user->personalinfo->street_main,
                'maritalStatus'  => $user->personalinfo->marital_status,
                'post_code'      => $user->personalinfo->post_code,
                'city'           => $user->personalinfo->town_city,
                'province'       => $user->personalinfo->province_state,
                'country'        => $user->personalinfo->country_of_residence,
                'nationality'    => $user->personalinfo->nationality,
                'telephone'      => $user->personalinfo->telephone,
                'mobile'         => $user->personalinfo->mobile,
                'nrc'            => $user->nrc,
                'passport'       => $user->passport,

            ];
        } else {
            $userPersonalDatails = '';
        }


        if ($user->sponser) {
            $sponserDetails = [
                'names'          => $user->sponser->full_name,
                'relationship'   => $user->sponser->relationship,
                'phone'          => $user->sponser->phone,
                'tel'            => $user->sponser->tel,
                'city'           => $user->sponser->city,
                'province'       => $user->sponser->province,
                'country'        => $user->sponser->country,
            ];
        } else {
            $sponserDetails = [];
        }

        if ($user->bookings) {

            foreach ($user->bookings as $booking) {

                if ($booking->bookingElapsed == 1) {
                    $elaped = "Expired";
                } else {
                    $elaped = "Active";
                }
                if ($booking->paid == 1) {
                    $status = "Paid";
                } else {
                    $status = "Not Paid";
                }

                $_booking = [
                    'id'              => $booking->id,
                    'room_id'         => $booking->roomID,
                    'quotation'       => $booking->quotationID,
                    'bookingElapsed'  => $elaped,
                    'duration'        => $booking->maxDuration . ' Days',
                    'paid'            => $status,
                    'hostel'          => $booking->room->hostel->name,
                    'room_number'     => $booking->room->roomNumber,
                    'status'          => $elaped,
                    'date'            => date('d-M-Y', strtotime($booking->created_at)),
                ];

                $_bookings[] = $_booking;
            }
        }
        if (empty($_bookings)) {
            $_bookings = [];
        }


        if ($user->educational_information) {
            $userEducationalDetails = [
                'high_school'           => $user->educational_information->high_school,
                'primary_school'        => $user->educational_information->primary_school,
                'highest_qualification' => $user->educational_information->highest_qualification,
                'attachments'           => $user->attachments,
            ];
        } else {
            $userEducationalDetails = [];
        }

        if ($user->student_id) {
            $student_id = $user->student_id;
        } else {
            $student_id = "";
        }
        $last_seen = Carbon::createFromTimestamp(strtotime($user->last_login_at))->diffForHumans();

        if ($user->application) {
            //$application = ApplicationForm::data($user->application->id);
        } else {
            $application = [];
        }

        $apID                =  self::getCurrentAcademicPeriodID($user->id);

        if ($user->programs) {
            foreach ($user->programs as $program) {

                $userProgram = UserProgram::where('userID', $user->id)->where('programID', $program->id)->get()->first();
                $_program = Programs::data($program->id, null, $userProgram->id);

                $_programs[] = $_program;
            }

            $userProgram = UserProgram::where('userID', $user->id)->get()->last();
            if ($userProgram) {
                $myClassIDs = [];
                $currentRegisteredClasses = [];
                $currentProgram     = Programs::data($userProgram->programID);
                $currentProgramName = $currentProgram['qualification'] . ' - ' . $currentProgram['name'];

                // Find the current courses
                $allEnrollments = Enrollment::where('userID', $user->id)->get();

                foreach ($allEnrollments as $aEnrollment) {
                    $myClassIDs[] = $aEnrollment->classID;
                }

                if (empty($myClassIDs)) {
                    $myClassIDs = [];
                }

                if ($myClassIDs) {
                    // Find the classes in this academic period
                    $classes = Classes::where('academicPeriodID', $apID)->whereIn('id', $myClassIDs)->get();

                    foreach ($classes as $acClass) {
                        $currentRegisteredClasses[] = Classes::data($acClass->id);
                    }
                }


                $mode               = UserStudyModes::where('userID', $user->id)->get()->last();

                if (!empty($mode)) {
                    $currentMode        = UserStudyModes::where('id', $mode->studyModeID)->get()->first();
                    $currentModeName    = $currentMode->name;
                } else {

                    $currentMode        = [];
                    $currentModeName    = 'No Study Mode Set';
                }
            }
        }
        if (empty($_programs)) {
            $_programs          = [];
            $currentProgram     = [];
            $currentProgramName = '';
            $currentMode        = '';
            $currentModeName    = '';
            $currentRegisteredClasses = [];
        }


        # Find academic Period
        $lastEnrolledClass = Enrollment::where('userID', $id)->get()->last();

        if ($lastEnrolledClass) {
            $academicPeriod    = AcademicPeriods::find(Classes::where('id', $lastEnrolledClass->classID)->first()->academicPeriodID);
            $booking = StudentRecord::where('userID', $id)->where('academicPeriodID', $academicPeriod->id)->get()->first();

            if ($academicPeriod->acEndDate > Carbon::now()) {
                $canBookForAccommodation = 1;
            } else {
                $canBookForAccommodation = 0;
            }

            if ($booking) {
                $hasAccommodation = 1;
            } else {
                $hasAccommodation = 0;
            }
        } else {
            $hasAccommodation = 0;
            $canBookForAccommodation = 0;
        }




        $AcademicProgression = General::getAcademicPaymentProgress($user->id, $apID);
        $percent             = round(Accounting::AcademicPaymentPercentage($user->id, $apID));



        // registration bypass
        $bypass = []; //Temp2::where('studentID', $user->student_id)->get()->first();

        if (!empty($bypass)) {
            $registrationBypass = 1;
        } else {
            $registrationBypass = 0;
        }

        // Check if student has registered for exam in the current academic period id
        $examRegistration = ExamRegistraion::where('userID', $id)->where('academicPeriodID', $apID)->get()->first();

        $hasRegistreredForExam = 'No';
        $canDownloadExamSlip   = 'No';

        if ($examRegistration && $examRegistration->id) {
            $hasRegistreredForExam = 'Yes';

            if ($examRegistration->status == 1) {
                $canDownloadExamSlip = 'Yes';
            }
        }

        $invoice = Invoice::where('user_id', $user->id)->get()->last();

        if (!empty($invoice)) {
            $lastInvoiceDate = $invoice->created_at->toFormattedDateString();
            $lastInvoiceAmount = $invoice->details->sum('ammount');
        } else {
            $lastInvoiceDate = '';
            $lastInvoiceAmount = '';
        }

        if ($currentProgram) {
            $progression = General::checkProgression($user->id, $currentProgram['id']);
        } else {
            $progression = [];
        }

        if ($apID) {
            $academicPeriod = AcademicPeriods::find($apID);
            $currentAcademicPeriodName = $academicPeriod->period->type;
        } else {
            $currentAcademicPeriodName = '';
        }

        $userPaymentPlan = UserPaymentPlan::where('userID', $user->id)->get()->last();
        $userPaymentPlanData = [];
        if ($userPaymentPlan) {
            $userPaymentPlanData = PaymentPlan::invoicePaymentPlanDetails($user->id, $apID, $userPaymentPlan->paymentPlanID);
        } else {
            $userPaymentPlanData = [];
        }


        $user = [
            'key'                      => $user->id,
            'id'                       => $user->id,
            'progression'              => $progression,
            'paymentPlanData'          => $userPaymentPlanData,
            'names'                    => $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name,
            'first_name'               => $user->first_name,
            'middle_name'              => $user->middle_name,
            'last_name'                => $user->last_name,
            'nrc'                      => $user->nrc,
            'initials'                 => substr($user->first_name, 0, 1) . '' . substr($user->last_name, 0, 1),
            'email'                    => strtolower($user->email),
            'gender'                   => $user->gender,
            'sms_id'                   => $user->guest_id,
            'student_id'               => $student_id,
            'balance'                  => (new General)->balance($user->id),
            'last_login'               => $user->last_login_at,
            'last_seen'                => $last_seen,
            'ip_address'               => $user->last_login_ip,
            'avatar'                   => "/user_photos/" . '' . $user->image,
            'avatarFullURL'            => env('APP_URL') . '/' . "/user_photos/" . '' . $user->image,
            'personal_information'     => $userPersonalDatails,
            'educational_information'  => $userEducationalDetails,
            'sponserDetails'           => $sponserDetails,
            'roomBookings'             => $_bookings,
            'online_application'       => $application,
            'enrolledPrograms'         => $_programs,
            'enrolledProgram'          => $currentProgram,
            'currentProgram'           => $currentProgram,
            'currentProgramName'       => $currentProgramName,
            'currentMode'              => $currentMode,
            'currentModeName'          => $currentModeName,
            'currentRegisteredClasses' => $currentRegisteredClasses,
            'currentAcademicPeriodName' => $currentAcademicPeriodName,
            'hasAccommodation'         => $hasAccommodation,
            'canBookForAccommodation'  => $canBookForAccommodation,
            'preActivated'             => $user->preActivated,
            'academicProgression'      => $AcademicProgression,
            'registrationByPass'       => $registrationBypass,
            'hasRegistreredForExam'    => $hasRegistreredForExam,
            'canDownloadExamSlip'      => $canDownloadExamSlip,
            'paymentPercentage'        => $percent,
            'currentAPID'              => $apID,
            'lastInvoiceDate'          => $lastInvoiceDate,
            'lastInvoiceAmount'        => $lastInvoiceAmount,
            'exemptions'               => Exemption::myExemptions($user->id),
            'admissionStatus'          => General::currentUserAdmissionStatusValue($user->id),
            'roles'                    => $user->getRoleNames(),
            'permissions'              => $user->getAllPermissions(),
        ];

        return $user;
    }


    public static function userFinancialAndAcademicStatus($id,$academicPeriodID)
    {
        $user = User::find($id);
        $student_id                 = '';
        $sponserDetails             = [];
        $userEducationalDetails     = [];
        $userPersonalDatails        = [];
        $application                = [];
        $_bookings                  = [];
        $currentProgram     = [];
        $currentProgramName = '';
        $currentMode        = '';
        $currentModeName    = '';
        $currentProgramID   = '';
        $last_seen                  = Carbon::createFromTimestamp(strtotime($user->last_login_at))->diffForHumans();

        if ($user->student_id) {
            $student_id = $user->student_id;
        }
        if ($user->programs) {
            $userProgram = UserProgram::where('userID', $user->id)->get()->last();
            if ($userProgram) {
                $currentProgram     = Programs::dataMini($userProgram->programID);
                $currentProgramName = $currentProgram['qualification'] . ' - ' . $currentProgram['name'];
                $currentProgramID   = $userProgram->programID;
                $mode               = UserStudyModes::where('userID', $user->id)->get()->last();

                if (!empty($mode)) {
                    $currentMode        = UserStudyModes::where('id', $mode->studyModeID)->get()->first();
                    $currentModeName    = $currentMode->name;
                } else {

                    $currentMode        = [];
                    $currentModeName    = 'No Study Mode Set';
                }
            }
        }


        # Find academic Period
        $lastEnrolledClass = Enrollment::where('userID', $id)->get()->last();

        if ($lastEnrolledClass) {
            $academicPeriod    = AcademicPeriods::find(Classes::where('id', $lastEnrolledClass->classID)->first()->academicPeriodID);
            $booking = StudentRecord::where('userID', $id)->where('academicPeriodID', $academicPeriod->id)->get()->first();

            if ($academicPeriod->acEndDate > Carbon::now()) {
                $canBookForAccommodation = 1;
            } else {
                $canBookForAccommodation = 0;
            }

            if ($booking) {
                $hasAccommodation = 1;
            } else {
                $hasAccommodation = 0;
            }
        } else {
            $hasAccommodation = 0;
            $canBookForAccommodation = 0;
        }

        $apID                =  General::getCurrentAcademicPeriodID($user->id);

        if ($apID) {
            $academicPeriod = AcademicPeriods::find($apID);
            $apCode = $academicPeriod->code;
        } else {
            $apCode = " ";
        }



        $AcademicProgression = General::getAcademicPaymentProgress($user->id, $apID);
        $percent             = round(Accounting::AcademicPaymentPercentage($user->id, $apID));

        # Exemptions
        $exemption = Exemption::where('userID', $user->id)->get();

        if (!empty($exemption)) {
            # code...
        }

        $invoice = Invoice::where('user_id', $user->id)->get()->last();

        if (!empty($invoice)) {
            $lastInvoiceDate = $invoice->created_at->toFormattedDateString();
            $lastInvoiceAmount = $invoice->details->sum('ammount');
        } else {
            $lastInvoiceDate = '';
            $lastInvoiceAmount = '';
        }


        $receipt = Receipt::where('user_id', $user->id)->get()->last();

        if (!empty($receipt)) {
            $lastReceiptDate = $receipt->created_at->toFormattedDateString();
            $lastReceiptAmount = $receipt->ammount_paid;
        } else {
            $lastReceiptDate = '';
            $lastReceiptAmount = '';
        }

        if ($currentProgram) {
            $progression = General::checkProgression($user->id, $currentProgram['id']);
            $yearOfStudy = $progression['currentLevelName'];
        } else {
            $progression = [];
            $yearOfStudy = '';
        }

        $userPaymentPlan = UserPaymentPlan::where('userID', $user->id)->get()->last();
        $userPaymentPlanData = [];
        if ($userPaymentPlan) {
            $userPaymentPlanData = PaymentPlan::invoicePaymentPlanDetails($user->id, $apID, $userPaymentPlan->paymentPlanID);
        } else {
            $userPaymentPlanData = [];
        }

        // Find all acadedmic periods for the user till the requested academic period and fetch results



        $user = [
            'key'                      => $user->id,
            'id'                       => $user->id,
            'names'                    => $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name,
            'first_name'               => $user->first_name,
            'middle_name'              => $user->middle_name,
            'last_name'                => $user->last_name,
            'initials'                 => substr($user->first_name, 0, 1) . '' . substr($user->last_name, 0, 1),
            'email'                    => strtolower($user->email),
            'gender'                   => $user->gender,
            'paymentPlanData'          => $userPaymentPlanData,
            'nrc'                      => $user->nrc,
            'sms_id'                   => $user->guest_id,
            'student_id'               => $student_id,
            'balance'                  => number_format((new General)->balance($user->id)),
            'last_login'               => $user->last_login_at,
            'last_seen'                => $last_seen,
            'ip_address'               => $user->last_login_ip,
            'avatar'                   => "/user_photos/" . '' . $user->image,
            'personal_information'     => $userPersonalDatails,
            'educational_information'  => $userEducationalDetails,
            'sponserDetails'           => $sponserDetails,
            'roomBookings'             => $_bookings,
            'online_application'       => '',
            'enrolledProgram'          => $currentProgram,
            'currentProgram'           => $currentProgram,
            'currentProgramName'       => $currentProgramName,
            'currentProgramID'         => $currentProgramID,
            'currentMode'              => $currentMode,
            'currentModeName'          => $currentModeName,
            'hasAccommodation'         => $hasAccommodation,
            'canBookForAccommodation'  => $canBookForAccommodation,
            'preActivated'             => $user->preActivated,
            'academicProgression'      => $AcademicProgression,
            'paymentPercentage'        => $percent,
            'currentAPID'              => $apID,
            'currentAPCode'            => $apCode,
            'lastInvoiceDate'          => $lastInvoiceDate,
            'lastInvoiceAmount'        => number_format(round($lastInvoiceAmount)),
            'lastReceiptDate'          => $lastReceiptDate,
            'lastReceiptAmount'        => number_format(round($lastReceiptAmount)),
            'progression'              => $progression,
            'admissionStatus'          => General::currentUserAdmissionStatusValue($user->id),
            'yearOfStudy'              => $yearOfStudy,
            'academicData'             => AcademicPeriods::myclassesTillProvidedAcademicPeriod($user->id,$academicPeriodID),
        ];

        return $user;
    }

    public static function currentUserAdmissionStatusValue($userID)
    {
        $admissionStatusValue   = '';
        $currentProgram         = UserProgram::where('userID', $userID)->get()->last();
        if (!empty($currentProgram)) {
            $admissionStatus        = StudentRecord::where('userID', $userID)->where('programID', $currentProgram->programID)->get()->last();
            if (!empty($admissionStatus)) {
                $admissionStatusValue   =  $admissionStatus->admissionStatus;
            } else {
                $admissionStatusValue   = '';
            }
        }

        return $admissionStatusValue;
    }
    public static function currentUserAdmissionStatus($userID)
    {
        $admissionStatus    = [];
        $currentProgram     = UserProgram::where('userID', $userID)->get()->last();
        $admissionStatus    = StudentRecord::where('userID', $userID)->where('programID', $currentProgram->id)->get()->first();

        return $admissionStatus;
    }

    public static function jsondataMini($id)
    {
        $user = User::find($id);
        $student_id                 = '';
        $sponserDetails             = [];
        $userEducationalDetails     = [];
        $userPersonalDatails        = [];
        $application                = [];
        $_bookings                  = [];
        $currentProgram     = [];
        $currentProgramName = '';
        $currentMode        = '';
        $currentModeName    = '';
        $currentProgramID   = '';
        $last_seen                  = Carbon::createFromTimestamp(strtotime($user->last_login_at))->diffForHumans();

        if ($user->student_id) {
            $student_id = $user->student_id;
        }
        if ($user->programs) {
            $userProgram = UserProgram::where('userID', $user->id)->get()->last();
            if ($userProgram) {
                $currentProgram     = Programs::dataMini($userProgram->programID);
                $currentProgramName = $currentProgram['qualification'] . ' - ' . $currentProgram['name'];
                $currentProgramID   = $userProgram->programID;
                $mode               = UserStudyModes::where('userID', $user->id)->get()->last();

                if (!empty($mode)) {
                    $currentMode        = UserStudyModes::where('id', $mode->studyModeID)->get()->first();
                    $currentModeName    = $currentMode->name;
                } else {

                    $currentMode        = [];
                    $currentModeName    = 'No Study Mode Set';
                }
            }
        }


        # Find academic Period
        $lastEnrolledClass = Enrollment::where('userID', $id)->get()->last();

        if ($lastEnrolledClass) {
            $academicPeriod    = AcademicPeriods::find(Classes::where('id', $lastEnrolledClass->classID)->first()->academicPeriodID);
            $booking = Booking::where('userID', $id)->where('academicPeriodID', $academicPeriod->id)->get()->first();

            if ($academicPeriod->acEndDate > Carbon::now()) {
                $canBookForAccommodation = 1;
            } else {
                $canBookForAccommodation = 0;
            }

            if ($booking) {
                $hasAccommodation = 1;
            } else {
                $hasAccommodation = 0;
            }
        } else {
            $hasAccommodation = 0;
            $canBookForAccommodation = 0;
        }

        $apID                =  General::getCurrentAcademicPeriodID($user->id);

        if ($apID) {
            $academicPeriod = AcademicPeriods::find($apID);
            $apCode = $academicPeriod->code;
        } else {
            $apCode = " ";
        }



        $AcademicProgression = General::getAcademicPaymentProgress($user->id, $apID);
        $percent             = round(self::AcademicPaymentPercentage($user->id, $apID));

        # Exemptions
        $exemption = \App\Models\Exemption::where('userID', $user->id)->get();

        if (!empty($exemption)) {
            # code...
        }

        $invoice = Invoice::where('user_id', $user->id)->get()->last();

        if (!empty($invoice)) {
            $lastInvoiceDate = $invoice->created_at->toFormattedDateString();
            $lastInvoiceAmount = $invoice->details->sum('ammount');
        } else {
            $lastInvoiceDate = '';
            $lastInvoiceAmount = '';
        }

        if ($currentProgram) {
            $progression = General::checkProgression($user->id, $currentProgram['id']);
        } else {
            $progression = [];
        }

        $userPaymentPlan = UserPaymentPlan::where('userID', $user->id)->get()->last();
        $userPaymentPlanData = [];
        if ($userPaymentPlan) {
            $userPaymentPlanData = PaymentPlan::invoicePaymentPlanDetails($user->id, $apID, $userPaymentPlan->paymentPlanID);
        } else {
            $userPaymentPlanData = [];
        }



        $user = [
            'key'                      => $user->id,
            'id'                       => $user->id,
            'names'                    => $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name,
            'first_name'               => $user->first_name,
            'middle_name'              => $user->middle_name,
            'last_name'                => $user->last_name,
            'initials'                 => substr($user->first_name, 0, 1) . '' . substr($user->last_name, 0, 1),
            'email'                    => strtolower($user->email),
            'gender'                   => $user->gender,
            'paymentPlanData'          => $userPaymentPlanData,
            'nrc'                      => $user->nrc,
            'sms_id'                   => $user->guest_id,
            'student_id'               => $student_id,
            'balance'                  => (new General)->balance($user->id),
            'last_login'               => $user->last_login_at,
            'last_seen'                => $last_seen,
            'ip_address'               => $user->last_login_ip,
            'avatar'                   => "/user_photos/" . '' . $user->image,
            'personal_information'     => $userPersonalDatails,
            'educational_information'  => $userEducationalDetails,
            'sponserDetails'           => $sponserDetails,
            'roomBookings'             => $_bookings,
            'online_application'       => '',
            'enrolledProgram'          => $currentProgram,
            'currentProgram'           => $currentProgram,
            'currentProgramName'       => $currentProgramName,
            'currentProgramID'         => $currentProgramID,
            'currentMode'              => $currentMode,
            'currentModeName'          => $currentModeName,
            'hasAccommodation'         => $hasAccommodation,
            'canBookForAccommodation'  => $canBookForAccommodation,
            'preActivated'             => $user->preActivated,
            'academicProgression'      => $AcademicProgression,
            'paymentPercentage'        => $percent,
            'currentAPID'              => $apID,
            'currentAPCode'            => $apCode,
            'lastInvoiceDate'          => $lastInvoiceDate,
            'lastInvoiceAmount'        => $lastInvoiceAmount,
            'progression'              => $progression,


        ];

        return $user;
    }

    public static function jsondataBasic($id)
    {

        $user = User::find($id);


        if ($user->personalinfo) {
            $userPersonalDatails = [

                'dob'            => $user->personalinfo->dob,
                'street'         => $user->personalinfo->street_main,
                'maritalStatus'  => $user->personalinfo->marital_status,
                'post_code'      => $user->personalinfo->post_code,
                'city'           => $user->personalinfo->town_city,
                'province'       => $user->personalinfo->province_state,
                'country'        => $user->personalinfo->country_of_residence,
                'nationality'    => $user->personalinfo->nationality,
                'telephone'      => $user->personalinfo->telephone,
                'mobile'         => $user->personalinfo->mobile,
                'nrc'            => $user->nrc,
                'passport'       => $user->passport_number,

            ];
        } else {
            $userPersonalDatails = '';
        }


        if ($user->sponser) {
            $sponserDetails = [
                'names'          => $user->sponser->full_name,
                'relationship'   => $user->sponser->relationship,
                'phone'          => $user->sponser->phone,
                'tel'            => $user->sponser->tel,
                'city'           => $user->sponser->city,
                'province'       => $user->sponser->province,
                'country'        => $user->sponser->country,
            ];
        } else {
            $sponserDetails = [];
        }




        if ($user->educational_information) {
            $userEducationalDetails = [
                'high_school'           => $user->educational_information->high_school,
                'primary_school'        => $user->educational_information->primary_school,
                'highest_qualification' => $user->educational_information->highest_qualification,
                'attachments'           => $user->attachments,
            ];
        } else {
            $userEducationalDetails = [];
        }

        if ($user->student_id) {
            $student_id = $user->student_id;
        } else {
            $student_id = "";
        }
        $last_seen = Carbon::createFromTimestamp(strtotime($user->last_login_at))->diffForHumans();

        if ($last_seen == "50 years ago") {
            $last_seen = '';
        }

        if ($user->programs) {
            foreach ($user->programs as $program) {
                $_program = Programs::data($program->id);
                $_programs[] = $_program;
            }

            $userProgram = UserProgram::where('userID', $user->id)->get()->last();
            if ($userProgram) {
                $currentProgram = Programs::data($userProgram->programID);
                $currentProgramName = $currentProgram['qualification'] . ' - ' . $currentProgram['name'];
            }
        }
        if (empty($_programs)) {
            $_programs = [];
            $currentProgram = [];
            $currentProgramName = '';
        }




        $apID                =  General::getCurrentAcademicPeriodID($user->id);

        $lastLogin = $user->last_login_at;

        if ($lastLogin == null) {
            $lastLogin = '';
        }


        $semesterStatus     = General::checkStudentsSemester($user->id);


        $user = [

            'id'                       => $user->id,
            'names'                    => $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name,
            'first_name'               => $user->first_name,
            'middle_name'              => $user->middle_name,
            'last_name'                => $user->last_name,
            'initials'                 => substr($user->first_name, 0, 1) . '' . substr($user->last_name, 0, 1),
            'email'                    => strtolower($user->email),
            'gender'                   => $user->gender,
            'sms_id'                   => $user->guest_id,
            'student_id'               => $student_id,
            'balance'                  => (new General)->balance($user->id),
            'last_login'               => $lastLogin,
            'last_seen'                => $last_seen,
            'ip_address'               => $user->last_login_ip,
            'avatar'                   => "/user_photos/" . '' . $user->image,
            'personal_information'     => $userPersonalDatails,
            'educational_information'  => $userEducationalDetails,
            'sponserDetails'           => $sponserDetails,
            'enrolledPrograms'         => $_programs,
            'enrolledProgram'          => $currentProgram,
            'currentProgram'           => $currentProgram,
            'currentProgramName'       => $currentProgramName,
            'semesterStatus'           => $semesterStatus,


        ];

        return $user;
    }

    public static function jsondataBasic2($id)
    {

        $user = User::find($id);


        if ($user->personalinfo) {
            $userPersonalDatails = [

                'dob'            => $user->personalinfo->dob,
                'street'         => $user->personalinfo->street_main,
                'maritalStatus'  => $user->personalinfo->marital_status,
                'post_code'      => $user->personalinfo->post_code,
                'city'           => $user->personalinfo->town_city,
                'province'       => $user->personalinfo->province_state,
                'country'        => $user->personalinfo->country_of_residence,
                'nationality'    => $user->personalinfo->nationality,
                'telephone'      => $user->personalinfo->telephone,
                'mobile'         => $user->personalinfo->mobile,
                'nrc'            => $user->nrc,
                'passport'       => $user->passport_number,

            ];
        } else {
            $userPersonalDatails = '';
        }


        if ($user->sponser) {
            $sponserDetails = [
                'names'          => $user->sponser->full_name,
                'relationship'   => $user->sponser->relationship,
                'phone'          => $user->sponser->phone,
                'tel'            => $user->sponser->tel,
                'city'           => $user->sponser->city,
                'province'       => $user->sponser->province,
                'country'        => $user->sponser->country,
            ];
        } else {
            $sponserDetails = [];
        }




        if ($user->educational_information) {
            $userEducationalDetails = [
                'high_school'           => $user->educational_information->high_school,
                'primary_school'        => $user->educational_information->primary_school,
                'highest_qualification' => $user->educational_information->highest_qualification,
                'attachments'           => $user->attachments,
            ];
        } else {
            $userEducationalDetails = [];
        }

        if ($user->student_id) {
            $student_id = $user->student_id;
        } else {
            $student_id = "";
        }
        $last_seen = Carbon::createFromTimestamp(strtotime($user->last_login_at))->diffForHumans();

        if ($last_seen == "50 years ago") {
            $last_seen = '';
        }

        if ($user->programs) {


            $userProgram = UserProgram::where('userID', $user->id)->get()->last();
            if ($userProgram) {
                $currentProgram = Programs::data($userProgram->programID);
                $currentProgramName = $currentProgram['qualification'] . ' - ' . $currentProgram['name'];
            }
        }
        if (empty($_programs)) {
            $_programs = [];
            $currentProgram = [];
            $currentProgramName = '';
        }






        $lastLogin = $user->last_login_at;

        if ($lastLogin == null) {
            $lastLogin = '';
        }


        $semesterStatus     = [];
        $userProgram = UserProgram::where('userID', $user->id)->get()->last();
        if ($userProgram && $userProgram->id) {
            $progression = General::checkProgression($user->id, $userProgram->programID);
        } else {
            $progression = [];
        }


        $user = [

            'id'                       => $user->id,
            'names'                    => $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name,
            'first_name'               => $user->first_name,
            'middle_name'              => $user->middle_name,
            'last_name'                => $user->last_name,
            'initials'                 => substr($user->first_name, 0, 1) . '' . substr($user->last_name, 0, 1),
            'email'                    => strtolower($user->email),
            'gender'                   => $user->gender,
            'sms_id'                   => $user->guest_id,
            'student_id'               => $student_id,
            'balance'                  => (new General)->balance($user->id),
            'last_login'               => $lastLogin,
            'last_seen'                => $last_seen,
            'ip_address'               => $user->last_login_ip,
            'avatar'                   => "/user_photos/" . '' . $user->image,
            'personal_information'     => $userPersonalDatails,
            'educational_information'  => $userEducationalDetails,
            'sponserDetails'           => $sponserDetails,
            'enrolledPrograms'         => $_programs,
            'enrolledProgram'          => $currentProgram,
            'currentProgram'           => $currentProgram,
            'currentProgramName'       => $currentProgramName,
            'progression'              => $progression,


        ];

        return $user;
    }


    public function searchUser(Request $request)
    {

        $q = request('searchKey');

        if (is_numeric($q)) {

            # Search for user by student id
            $users = User::search($q)->take(30)->get();
            $nrcs     = UserPersonalInformation::search($q)->take(30)->get();
            if (!empty($users)) {
                foreach ($users as $user) {
                    $user = General::jsondata($user->id);
                    $users_[] = $user;
                }

                if (!empty($users_)) {
                    return $users_;
                } else {
                    $errors = [
                        'error' => 'No match found',
                    ];
                    return response()->json([
                        'status' => 'error',
                        'errors' => $errors,
                    ], 422);
                }
            }


            # search for user by NRC Number
            if (!empty($nrcs)) {

                foreach ($nrcs as $nrc) {

                    $user = $nrc->user;
                    $user = General::jsondata($nrc->user->id);
                    $users[] = $user;
                }
                if (!empty($users)) {
                    return $users;
                } else {
                    $errors = [
                        'error' => 'No match found',
                    ];
                    return response()->json([
                        'status' => 'error',
                        'errors' => $errors,
                    ], 422);
                }
            }
        } else {
            $users = User::search($q)->take(30)->get();

            if (!empty($users)) {
                foreach ($users as $user) {
                    $user = General::jsondata($user->id);
                    $users_[] = $user;
                }

                if (!empty($users_)) {
                    return $users_;
                } else {
                    $errors = [
                        'error' => 'No match found',
                    ];
                    return response()->json([
                        'status' => 'error',
                        'errors' => $errors,
                    ], 422);
                }
            }
        }
    }


    public static function userBalanceReport($userID)
    {

        $user = User::find($userID);
        if ($user->student_id) {
            $student_id = $user->student_id;
        } else {
            $student_id = "";
        }

        return [
            'id'                       => $user->id,
            'names'                    => $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name,
            'first_name'               => $user->first_name,
            'avatarFullURL'            => env('APP_URL') . '/' . "/user_photos/" . '' . $user->image,
            'middle_name'              => $user->middle_name,
            'last_name'                => $user->last_name,
            'initials'                 => substr($user->first_name, 0, 1) . '' . substr($user->last_name, 0, 1),
            'email'                    => strtolower($user->email),
            'gender'                   => $user->gender,
            'sms_id'                   => $user->guest_id,
            'student_id'               => $student_id,
            'balance'                  => (new General)->balance($user->id),
            'balanceReadable'          => env('BILLING_CURRENCY') . ' ' . number_format((new General)->balance($user->id)),
        ];
    }

    public static function userBalanceReportWithPaymentPlan($userID)
    {

        $user                   = User::find($userID);
        $status                 = 'Closed';
        $canAttendClass         = 'No';
        $percent                = 0;
        $apCode                 = '';
        $suspended              = 1;
        $userPaymentPlanData    = [];
        $academicProgression    = '';


        if ($user->student_id) {
            $student_id = $user->student_id;
        } else {
            $student_id = '';
        }

        // Return the current Academic Period
        $apID                = General::getCurrentAcademicPeriodID($userID);

        if ($apID) {
            $academicPeriod = AcademicPeriods::find($apID);

            if ($academicPeriod) {
                if (strtotime($academicPeriod->acEndDate) > strtotime(date("Y-m-d"))) {
                    $status = 'Open';
                } else {
                    $status = 'Closed';
                }
            }

            $apCode              = $academicPeriod->code;
            $academicProgression = General::getAcademicPaymentProgress($user->id, $apID);
            $percent             = round(self::AcademicPaymentPercentage($user->id, $apID));
            $userPaymentPlan     = UserPaymentPlan::where('userID', $userID)->get()->last();

            if ($userPaymentPlan) {
                $userPaymentPlanData = PaymentPlan::invoicePaymentPlanDetails($userID, $apID, $userPaymentPlan->paymentPlanID);

                if ($userPaymentPlanData['canAttendClass'] == 1) {
                    $suspended = 0;
                    $canAttendClass = 'Yes';
                }
            }
        }

        $userProgram = UserProgram::where('userID', $userID)->get()->last();
        $programName = '';
        if ($userProgram) {
            $program = Programs::dataMini($userProgram->programID);
            $programName = $program['fullname'];
        }

        return [
            'id'                       => $user->id,
            'names'                    => $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name,
            'first_name'               => $user->first_name,
            'avatarFullURL'            => env('APP_URL') . '/' . "/user_photos/" . '' . $user->image,
            'middle_name'              => $user->middle_name,
            'last_name'                => $user->last_name,
            'initials'                 => substr($user->first_name, 0, 1) . '' . substr($user->last_name, 0, 1),
            'email'                    => strtolower($user->email),
            'gender'                   => $user->gender,
            'sms_id'                   => $user->guest_id,
            'student_id'               => $student_id,
            'balance'                  => (new General)->balance($user->id),
            'percent'                  => $percent,
            'academicProgression'      => $academicProgression,
            'academicPeriodStatus'     => $status,
            'currentProgramName'       => $programName,
            'academicPeriodCode'       => $apCode,
            'suspended'                => $suspended,
            'canAttendClass'           => $canAttendClass,
            'userPaymentPlanData'      => $userPaymentPlanData,
            'balanceReadable'          => env('BILLING_CURRENCY') . ' ' . number_format((new General)->balance($user->id)),
        ];
    }




    public function storePersonalInformation(Request $request)
    {

        // some validation
        $this->validate(request(), [

            'dob'                     => 'required',
            'street_main'             => 'required',
            'town_city'               => 'required',
            'province_state'          => 'required',
            'country_of_residence'    => 'required',
            'nationality'             => 'required',
            'mobile'                  => 'required',
            'marital_status'          => 'required'

        ]);

        $user_id = request('user_id');
        $dob = Carbon::parse(request('dob'));

        try {
            DB::beginTransaction();

            # Check if existing
            $personalInfo = UserPersonalInformation::where('user_id', $user_id);

            UserPersonalInformation::create([

                'user_id'                => $user_id,
                'dob'                    => $dob,
                'marital_status'        => request('marital_status'),
                'street_main'             => request('street_main'),
                'town_city'               => request('town_city'),
                'province_state'           => request('province_state'),
                'country_of_residence'  => request('country_of_residence'),
                'nationality'           => request('nationality'),
                'telephone'               => request('telephone'),
                'mobile'                 => request('mobile'),

            ]);

            // update user table
            $User = User::find($user_id);
            $User->personal_information_submit = 1;
            $User->save();


            $k = $User->next_of_kin_submit;
            $p = $User->personal_information_submit;
            $e = $User->education_submit;
            $d = 1;

            if ($k + $p + $e + $d == 4) {
                // update user to state profile complete
                $User = User::find($user_id);
                $User->profile_complete = 1;
                $User->save();
            }


            DB::commit();
            return $status = General::guestStatusStatic($user_id);
        } catch (\Exception $e) {

            DB::rollback();

            return response()->json([
                'status' => 'error',
                'errors' => $e->getMessage()
            ], 422);
        }
    }

    public function storeEducationalInformation(Request $request)
    {


        // some validation
        $this->validate(request(), [
            'high_school'             => 'required',
            'primary_school'         => 'required',
            'highest_qualification'  => 'required',

        ]);

        try {
            DB::beginTransaction();

            $user_id = request('user_id');

            // save educational information
            /*
            EducationalInfo::create([
                'user_id'                => $user_id,
                'high_school'             => request('high_school'),
                'primary_school'        => request('primary_school'),
                'highest_qualification' => request('highest_qualification'),
            ]);*/

            // update user table
            $User = User::find($user_id);
            $User->education_submit = 1;
            $User->save();

            $k = $User->next_of_kin_submit;
            $p = $User->personal_information_submit;
            $e = $User->education_submit;
            $d = 1;

            if ($k + $p + $e + $d == 4) {
                // update user to state profile complete
                $User = User::find($user_id);
                $User->profile_complete = 1;
                $User->save();
            }

            DB::commit();
            return $status = General::guestStatusStatic($user_id);
        } catch (\Exception $e) {

            DB::rollback();

            return response()->json([
                'status' => 'error',
                'errors' => $e->getMessage()
            ], 422);
        }
    }
    public function storeNextofKeekInformation(Request $request)
    {
        // some validation
        $this->validate(request(), [
            'full_name'            => 'required',
            'relationship'        => 'required',
            'tel'                 => 'required',
            'phone'             => 'required',
            'city'                => 'required',
            'province'             => 'required',
            'country'             => 'required',

        ]);

        try {
            DB::beginTransaction();
            // find the user instance
            $user_id = request('user_id');

            // save educational information
            /*
            UserSponser::create([
                'user_id'            => $user_id,
                'full_name'         => request('full_name'),
                'relationship'        => request('relationship'),
                'tel'                 => request('tel'),
                'phone'                => request('phone'),
                'city'                => request('city'),
                'province'            => request('province'),
                'country'            => request('country'),
            ]);*/

            // update user table
            $User = User::find($user_id);
            $User->next_of_kin_submit = 1;
            $User->save();

            $k = $User->next_of_kin_submit;
            $p = $User->personal_information_submit;
            $e = $User->education_submit;
            $d = 1;

            if ($k + $p + $e + $d == 4) {
                // update user to state profile complete
                $User = User::find($user_id);
                $User->profile_complete = 1;
                $User->save();
            }


            DB::commit();
            return $status = General::guestStatusStatic($user_id);
        } catch (\Exception $e) {

            DB::rollback();

            return response()->json([
                'status' => 'error',
                'errors' => $e->getMessage()
            ], 422);
        }
    }

    public function guestStatus($id)
    {
        $user = User::find($id);

        if ($user->education_submit == 1) {
            $edu = 25;
        } else {
            $edu = 0;
        }

        if ($user->personal_information_submit == 1) {
            $pi = 25;
        } else {
            $pi = 0;
        }

        if ($user->next_of_kin_submit == 1) {
            $ni = 25;
        } else {
            $ni = 0;
        }

        if ($user->application_submited == 1) {
            $as = 25;
        } else {
            $as = 0;
        }

        $applicationStatus = $edu + $pi + $ni + $as;

        $status = [
            'user' => $user,
            'applicationStatus' => $applicationStatus,
        ];

        return $status;
    }

    public static function guestStatusStatic($id)
    {
        $user = User::find($id);

        if ($user->education_submit == 1) {
            $edu = 25;
        } else {
            $edu = 0;
        }

        if ($user->personal_information_submit == 1) {
            $pi = 25;
        } else {
            $pi = 0;
        }

        if ($user->next_of_kin_submit == 1) {
            $ni = 25;
        } else {
            $ni = 0;
        }

        if ($user->application_submited == 1) {
            $as = 25;
        } else {
            $as = 0;
        }

        $applicationStatus = $edu + $pi + $ni + $as;

        $status = [
            'user' => $user,
            'applicationStatus' => $applicationStatus,
        ];

        return $status;
    }

    public function userPermissions($id)
    {
        $user = User::find($id);

        foreach ($user->roles as $role) {

            $role_name = $role->slug;
            $role_names[] = $role_name;
        }
        if (empty($role_names)) {
            return " ";
        } else {
            return $role_names;
        }
    }

    public function autheticateRegisration(Request $request)
    {

        $this->validate(request(), [
            'password'            => 'required',
        ]);

        $user = User::find(request('userid'));

        $password = 'Incorrect Password. Provide the password that you use to login';

        $error = [
            'password' => $password,
            'message' => $password,
        ];

        if (Hash::check(request('password'), $user->password)) {
            return "true";
        } else {
            return response()->json([
                'errors' => $error,
            ], 422);
        }
    }


    public function updateUserProfile(Request $request)
    {

        $user = User::find(request('user_id'));

        $userPassport =  UserPersonalInformation::where('user_id',request('user_id'))->get()->first();
        if(!empty($userPassport->passport_number)){

        }else{
            if(request('passport')){

            }

        }

        // check if email or NRC is taken
        $errors = [];
        $userAccount = User::where('nrc', request('nrc'))->get()->first();

        if (!empty($userAccount) && $userAccount->id != $user->id) {
            $errors = [
                'nrc' => 'The NRC Number has been taken by ' . '' . $userAccount->guest_id . ' - ' . $userAccount->first_name . ' ' . $userAccount->last_name,
            ];
        }

        $userAccount = User::where('email', request('email'))->get()->first();



        if ($userAccount && $userAccount->id != $user->id) {

            $errors = [
                'email' => 'The email address provided has been taken by ' . '' . $userAccount->guest_id . ' - ' . $userAccount->first_name . ' ' . $userAccount->last_name,
            ];
        }

        if (!empty($errors)) {
            return response()->json([
                'errors' => $errors,
            ], 422);
        }

        $user->first_name                         = request('first_name');
        $user->middle_name                        = request('middle_name');
        $user->last_name                          = request('last_name');
        $user->email                              = request('email');
        $user->nrc                                = request('nrc');
        $user->passport                           = request('passport');
        $user->gender                             = request('gender');


        if ($user->personalinfo) {
            $dob = Carbon::parse(request('dob'));
            $user->personalinfo->passport_number                    = request('passport');
            $user->personalinfo->dob = $dob;
            $user->personalinfo->save();
        }


        if (!empty($user->sponser)) {
            $user->sponser->full_name             = request('names');
            $user->sponser->relationship          = request('relationship');
            $user->sponser->phone                 = request('phone');
            $user->sponser->tel                   = request('tel');
            $user->sponser->city                  = request('city');
            $user->sponser->province              = request('province');
            $user->sponser->country               = request('country');
            $user->sponser->save();
        } else {
            /*
             * To be updated
             * UserSponser::create([
                'user_id'            => $user->id,
                'full_name'          => request('names'),
                'relationship'       => request('relationship'),
                'tel'                => request('tel'),
                'phone'              => request('phone'),
                'city'               => request('city'),
                'province'           => request('province'),
                'country'            => request('country'),
            ]);*/
        }


        $user->save();
    }


    public static function getAcademicPaymentProgress($userID, $AcademicPeriodID)
    {

        $percent = Accounting::AcademicPaymentPercentage($userID, $AcademicPeriodID);

        if ($percent <= 50) {
            return  0;
        }

        if ($percent > 50 && $percent <= 80) {
            return 1;
        }
        if ($percent > 80 && $percent <= 99) {
            return 2;
        }

        if ($percent >= 100) {
            return 3;
        }
    }


    public static function getCurrentAcademicPeriodID($userID)
    {

        $enrollments = Enrollment::where('userID', $userID)->get();
        if ($enrollments) {

            foreach ($enrollments as $enrollment) {

                $apIDs[] = $enrollment->class->academicPeriodID;
            }

            if (!empty($apIDs)) {
                // Find the most recent academic period.
                $academicPeriod = AcademicPeriods::whereIn('id', $apIDs)->orderBy('acStartDate')->get()->last();

                return $academicPeriod->id;
            }
        }
    }

    public static function checkStudentsSemester($userID)
    {

        # Check the semester level of the student.
        # 1 current is the first so dont bill next invoice with madetory fees
        # 0 current is the second semester. So add mandetory fees to the next invoice

        $currentAcademicPeriodID = General::getCurrentAcademicPeriodID($userID);
        $checkStatus             = Invoicing::checkIfInvoiceHasMadetoryFees($userID, $currentAcademicPeriodID);

        return $checkStatus;
    }


    public static function getUserPaymentPercentage($userID, $academicID)
    {

        $AcademicProgression = General::getAcademicPaymentProgress($userID, $academicID);
        $percent             = round(Accounting::AcademicPaymentPercentage($userID, $academicID));

        return $percent;
    }

    public static function currentProgramData($userID)
    {



        $userProgram = UserProgram::where('userID', $userID)->get()->last();
        if ($userProgram) {
            $currentProgram     = Programs::dataMini($userProgram->programID);
            $currentProgramName = $currentProgram['qualification'] . ' - ' . $currentProgram['name'];
            $mode               = UserStudyModes::where('userID', $userID)->get()->last();

            if (!empty($mode)) {
                $currentMode        = UserStudyModes::where('id', $mode->studyModeID)->get()->first();
                $currentModeName    = $currentMode->name;
                $qualification      = $currentProgram['qualification'];
            } else {

                $currentMode        = [];
                $currentModeName    = 'No Study Mode Set';
                $qualification      = $currentProgram['qualification'];
            }
        }

        if (empty($userProgram)) {

            $currentProgram     = [];
            $currentProgramName = '';
            $currentMode        = '';
            $currentModeName    = '';
            $qualification      = '';
        }

        return  [

            'currentProgram'        => $currentProgram,
            'currentProgramName'    => $currentProgramName,
            'currentModeName'       => $currentModeName,
            'qualification'         => $qualification,
        ];
    }


    public static function userProgression($id, $programID)
    {

        # Find all academic periods that the user has been enrolled in
        $enrollments = Enrollment::where('userID', $id)->get();
        $userMode    = UserStudyModes::where('userID', $id)->get()->first();

        foreach ($enrollments as $enrollment) {
            $apIDs[] = $enrollment->class->academicPeriodID;
            $courseIDs[] = $enrollment->class->courseID;
        }
        //return $courseIDs;


        if (!empty($apIDs)) {



            # Find academic periods
            $aps = AcademicPeriods::whereIn('id', $apIDs)->get()->unique('id');
            if (empty($aps)) {
                $aps = [];
            }

            foreach ($aps as $ap) {

                // find the courses that are running for this program in this academic period and then count the number of courses passed ....??
                $classes = Classes::where('academicPeriodID', $ap->id)->get();

                /*if ($classes) {

                    foreach ($classes as $class) {
                        $courseIDs[] = $class->courseID;
                    }
                }*/
            }

            $programCourses = ProgramCourses::where('programID', $programID)->whereIn('courseID', $courseIDs)->get()->unique('level_id');


            foreach ($programCourses as $programCourse) {
                $courseLevel = CourseLevels::find($programCourse->level_id);

                $_programCourses = ProgramCourses::where('programID', $programID)->where('level_id', $courseIDs)->get();

                foreach ($_programCourses as $_programCourse) {

                    //$setCourseIDs[] = $programCourse;

                }
                // Check if each course run in the academic period.

                $levels[] = $courseLevel;
            }


            return [
                'academicPeriods' => $aps->count(),
                'levels'          => $levels,

            ];
        }
    }

    public static function checkProgression($userID, $programID)
    {

        $enrollments    = Enrollment::where('userID', $userID)->get();

        foreach ($enrollments as $enrollment) {

            $grade = Progression::calculateTotalGrade($userID, $enrollment->classID);
            $programCourse_ = ProgramCourses::where('courseID', $enrollment->class->course->id)->where('programID', $programID)->get()->first();

            if ($programCourse_) {
                $pcLevelID = $programCourse_->level_id;
            } else {
                $pcLevelID = '';
            }

            if ($enrollment->class && $grade) {
                $courseID  = $enrollment->class->course->id;
                $gradeType = $grade['type'];
                $gradeMark = $grade['mark'];
                // find level id

            } else {
                $courseID = '';
                $gradeType = '';
                $gradeMark = '';
            }

            $data = [
                'classID'   => $enrollment->classID,
                'courseID'  => $courseID,
                'level_id'  => $pcLevelID,
                'type'      => $gradeType,
                'mark'      => $gradeMark,
            ];

            $enrollmentsData[] = $data;
        }

        if (empty($enrollmentsData)) {
            $enrollmentsData = [];
        }


        // get all the levels in a program and sort the courses by level
        $programCourses = ProgramCourses::where('programID', $programID)->orderBy('level_id')->get()->unique('level_id');
        $levels = [];
        foreach ($programCourses as $programCourse) {

            // Find the courses that are attached to this level

            // Need to stop the loop based on the courses enrolled ?? How
            foreach ($enrollmentsData as $enrollmentData) {
                if ($enrollmentData['level_id'] == $programCourse->level_id) {
                    $levels[] = $enrollmentData;
                }
            }
        }

        if (!empty($levels)) {
            $lastLevelID        = array_pop($levels)['level_id'];
            $currentLevelName   = CourseLevels::find($lastLevelID)->name;
        } else {
            $levels = [];
            $currentLevelName = '';
        }

        return [
            'currentLevelName'  => $currentLevelName,
            'lastLevelArray'  => array_pop($levels),
            'levels'          => $levels,
            'programCourses'  => $programCourses,
        ];
    }
}