<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Academics\Programs;
use App\Models\Academics\ResultsImport;
use App\Models\Accounting\CreditNote;
use App\Models\Accounting\Invoice;
use App\Models\Accounting\ProfomaInvoice;
use App\Models\Accounting\Quotation;
use App\Models\Accounting\Receipt;
use App\Models\Accounting\Statement;
use App\Models\Admissions\EducationalInfo;
use App\Models\Admissions\Student;
use App\Models\Admissions\StudentRecord;
use App\Models\Admissions\UserPersonalInformation;
use App\Models\Admissions\UsersNextOfKin;
use App\Models\Admissions\UserSponser;
use App\Models\Applications\Booking;
use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User
 *
 * @property int $id
 * @property int $role_id
 * @property string $guest_id
 * @property string $first_name
 * @property string|null $middle_name
 * @property string $last_name
 * @property string|null $gender
 * @property string|null $image
 * @property int $education_submit
 * @property int $next_of_kin_submit
 * @property int $personal_information_submit
 * @property int $profile_complete
 * @property string|null $email
 * @property mixed|null $password
 * @property string|null $google_id
 * @property string|null $facebook_id
 * @property string|null $avatar_original
 * @property int $active
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $account_ready
 * @property string|null $_token
 * @property string|null $passport
 * @property string|null $nrc
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $last_login_at
 * @property string|null $last_login_ip
 * @property int|null $student_id
 * @property int $application_submited
 * @property int $preActivated
 * @property int|null $force_password_reset
 * @property string $admissionStatus
 * @property string $user_type
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAccountReady($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAdmissionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereApplicationSubmited($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatarOriginal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEducationSubmit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFacebookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereForcePasswordReset($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGoogleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGuestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLoginIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNextOfKinSubmit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNrc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePersonalInformationSubmit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePreActivated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfileComplete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification>
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken>
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'gender','image',
        'email', 'password', 'passport', 'nrc','user_type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function personalinfo()
    {
        return $this->hasOne(UserPersonalInformation::class,'user_id');
    }
    public function nextofKin()
    {
        return $this->hasOne(UsersNextOfKin::class,'user_id');
    }
    public function studentrecord()
    {
        return $this->hasOne(StudentRecord::class,'user_id');
    }

    public function creditnotes()
    {
        return $this->hasMany(CreditNote::class, 'user_id', 'id');
    }
//    public function offerletter()
//    {
//        return $this->hasOne(OfferLetter::class, 'user_id', 'id');
//    }
    public function programs()
    {
        return $this->belongsToMany(Programs::class, 'ac_userPrograms', 'userID', 'programID');
    }
    public function currentProgram()
    {
        return $this->hasOne(Programs::class, 'ac_userPrograms', 'userID', 'programID');
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'userID', 'id');
    }

//    public function collections()
//    {
//        return $this->hasOne(Collection::class);
//    }


//    public function registrationPendingProfessional()
//    {
//        return $this->hasOne(RegistrationApproval::class)->latest();
//    }

    public function registration()
    {
        return $this->hasMany(Registration::class);
    }

//    public function professional()
//    {
//        return $this->hasOne(ProfessionalSubscription::class, 'user_id', 'id');
//    }

//    public function subjects()
//    {
//        return $this->hasMany(RegisteredSubjects::class, 'user_id', 'id');
//    }

//    public function billing_subscription()
//    {
//        return $this->hasOne(BillingSubscription::class, 'user_id', 'id');
//    }

    public static function unpaid_invoice()
    {
        $last_quotation = Quotation::get()->where('user_id', Auth::user()->id)->where('status', 1)->last();
        return $last_quotation;
    }



//    public function applications()
//    {
//        return $this->hasMany(Application::class)->latest();
//    }

//    public function application()
//    {
//        return $this->hasOne(ApplicationForm::class)->latest();
//    }

    // fetch the most recent application
//    public function mostrecent_application()
//    {
//        return $this->hasOne(Application::class);
//    }

    public function last_quotation()
    {
        return $this->hasOne(Quotation::class)->latest();
    }


//    public function attachments()
//    {
//        return $this->hasMany(EducationalAttachment::class);
//    }

//    public function personalinfo()
//    {
//        return $this->hasOne(Personalinfo::class);
//    }

    public function educational_information()
    {
        return $this->hasOne(EducationalInfo::class);
    }

    public function sponsor()
    {
        return $this->hasOne(UserSponser::class);
    }

//    public function schoolarship()
//    {
//        return $this->hasMany(schoolarship::class);
//    }


    // accounting functions
    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    public function invoice()
    {
        return $this->hasOne(Invoice::class)->latest();
    }

    public function ProfomaInvoices()
    {
        return $this->hasMany(ProfomaInvoice::class);
    }
    public function Profomainvoice()
    {
        return $this->hasOne(ProfomaInvoice::class)->latest();
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function statement()
    {
        return $this->hasMany(Statement::class);
    }

//    public function enrollments()
//    {
//        return $this->hasMany(AcademicEnrollment::class, 'userID', 'id');
//    }
    public function lastEnrollment()
    {
        return $this->hasOne(Enrollment::class)->latest();
    }
    public function lastInvoice()
    {
        return $this->hasMany(Invoice::class)->latest();
    }


    // create guest id
    public function createGuestID()
    {
        // guest id sms1710-0001
        $year    = date("Y");
        $month   = date("m");

        // fetch lastid
        $lastID  = User::get()->last();
        $lastID  = $lastID->id + 1;

        $guestID = "SMS" . $year . $month . "000" . $lastID;
        return $guestID;
    }



    public static function status($id)
    {

        // $status = DB::select('SELECT examslip_status as status
        //           FROM students
        //           WHERE
        //           user_id = ?',[$id])->get()->first();

        $status = Student::where('user_id', $id)->get()->first();

        if ($status->examslip_status == 0)
            return 0;
        elseif ($status->examslip_status == 1) {
            return 1;
        } else {
            return 20;
        }
    }

    // function for getting user roles

    public function checkRole($id)
    {
        $users = User::where('role', $id);
        return $users;
    }
//    public function lastarchived_application()
//    {
//        return $this->hasOne(Application::class);
//    }



    public function applied_before()
    {

        $id = Auth::user()->id;

        $application = Application::where('user_id', $id)->get();

        if ($application->isEmpty()) {
            return 0;
        } else {
            return 1;
        }
    }

    public static function check_applied_before($id)
    {



        $application = Application::all()->where('user_id', $id);

        if (empty($application)) {
            return 0;
        } else if (!empty($application)) {
            return 1;
        }
    }



    // this function works only with the guest account.
    // it guides the application progress bar.

    public function checkprofilestatus()
    {

        if (Auth::user()->education_submit == 0) {
            $educationPercentage = 0;
        } else {
            $educationPercentage = 25;
        }

        if (Auth::user()->next_of_kin_submit == 0) {
            $kinPercentage = 0;
        } else {
            $kinPercentage = 25;
        }


        if (Auth::user()->personal_information_submit == 0) {
            $pinfoPercentage = 0;
        } else {
            $pinfoPercentage = 25;
        }

        $total = $educationPercentage + $kinPercentage + $pinfoPercentage + 25;

        $total = substr($total, 0, 3);

        return $total;
    }



    // student issues





    public static function findUser($id)
    {
        $value = User::find($id)->get();
        return $value;
    }

    public function enrollment()
    {
        return $this->hasOne(Enrollment::class)->latest();
    }




    // reports on users

    public static function numberOfGuests($id)
    {
        return $value = User::where('role_id', $id)->count();
    }


    public static function yearlyGuests($year)
    {


        $data = [

            $january   = User::whereYear('created_at', $year)->whereMonth('created_at', '=', '1')->count(),
            $feb       = User::whereYear('created_at', $year)->whereMonth('created_at', '=', '2')->count(),
            $march     = User::whereYear('created_at', $year)->whereMonth('created_at', '=', '3')->count(),
            $april     = User::whereYear('created_at', $year)->whereMonth('created_at', '=', '4')->count(),
            $may       = User::whereYear('created_at', $year)->whereMonth('created_at', '=', '5')->count(),
            $june      = User::whereYear('created_at', $year)->whereMonth('created_at', '=', '6')->count(),
            $july      = User::whereYear('created_at', $year)->whereMonth('created_at', '=', '7')->count(),
            $auguest   = User::whereYear('created_at', $year)->whereMonth('created_at', '=', '8')->count(),
            $september = User::whereYear('created_at', $year)->whereMonth('created_at', '=', '9')->count(),
            $october   = User::whereYear('created_at', $year)->whereMonth('created_at', '=', '10')->count(),
            $november  = User::whereYear('created_at', $year)->whereMonth('created_at', '=', '11')->count(),
            $december  = User::whereYear('created_at', $year)->whereMonth('created_at', '=', '12')->count(),

        ];

        return $data;
    }



    public function results()
    {
        return $this->hasMany(ResultsImport::class, 'user_id', 'id');
    }
    public static function check_for_with_exam_draw($user_id)
    {
        $_course = ResultsImport::where('user_id', $user_id)->where('mark', '-2')->get()->first();

        if ($_course) {
            return 1;
        }
    }

    public static function check_for_disqualification($user_id)
    {
        $_course = ResultsImport::where('user_id', $user_id)->where('mark', '-3')->get()->first();

        if ($_course) {
            return 1;
        }
    }


    public static function checkResultsStatus($id)
    {
        $user = ResultsImport::where('user_id', $id)->get()->first();
        return $user;
    }


    # Disable registration link for 1 months
    public static function disableRegistration()
    {

        $user = Auth::user();

        $lastRegistrationDate = $user->lastEnrollment->created_at->addDays(30);
        $today                = date('Y-m-d H:m:s');

        $today                = strtotime($today) * 1000;
        $lastRegistrationDate = strtotime($lastRegistrationDate) * 1000;

        if ($today >= $lastRegistrationDate) {
            return 1; # student is eligible for registration.
        } else {
            return 0; # student is not eligible for new registration
        }

        return $today;
    }


    # Disable registration link for 1 months
    public static function activateRoomBooking()
    {

        $user = Auth::user();

        $lastRegistrationDate = $user->lastEnrollment->created_at->addDays(30);
        $today                = date('Y-m-d H:m:s');

        $today                = strtotime($today) * 1000;
        $lastRegistrationDate = strtotime($lastRegistrationDate) * 1000;

        if ($today >= $lastRegistrationDate) {
            return 1; # student is not eligible for room booking.
        } else {
            return 0; # student is eligible for room booking
        }

        return $today;
    }

    public static function twoEnrollments()
    {
        $enrollments = Enrollment::where('user_id', Auth::user()->id)->where('year', 19)->count();

        if ($enrollments >= 1) {
            return 0;
        } else {
            return 1;
        }
    }




    public function resultsAvailability($user_id)
    {

        $user    = User::find($user_id);
        $results = ResultsImport::where('user_id', $user->id)->get()->first();

        return $results;
    }

}
