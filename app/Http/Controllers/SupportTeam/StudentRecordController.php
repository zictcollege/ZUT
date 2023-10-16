<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Custom\SuperAdmin;
use App\Http\Middleware\Custom\TeamSA;
use App\Http\Requests\UserRequest;
use App\Models\Admissions\StudentRecord;
use App\Repositories\IntakesRepo;
use App\Repositories\NationalitiesRepo;
use App\Repositories\ProgramsRepo;
use App\Repositories\QualificationsRepo;
use App\Repositories\StatesRepo;
use App\Repositories\StudentRecords;
use App\Repositories\StudyModeRepo;
use App\Repositories\TownsRepo;
use App\Repositories\UserModesRepo;
use App\Repositories\UserPaymentPlan;
use App\Repositories\UserProgramsRepo;
use App\Repositories\UserRepo;
use App\Support\General;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $statesRepo,$townsRepo,$nationalitiesRepo,$qualificationRepo,$programsRepo,
                $studyModeRepo,$intakeRepo,$typeRepo,$user,$studentRecordsRepo,$userProgramsRepo,$userModesRepo,
        $userPaymentPlan;


    public function __construct(NationalitiesRepo $nationalities,StatesRepo $statesRepo,TownsRepo $townsRepo,
    QualificationsRepo $qualificationRepo,ProgramsRepo $programsRepo,StudyModeRepo $studyModeRepo,IntakesRepo $intakeRepo,UserRepo $user,
    StudentRecords $studentRecordsRepo,UserProgramsRepo $userProgramsRepo,UserModesRepo $userModesRepo,UserPaymentPlan $userPaymentPlan)
    {
        $this->middleware(TeamSA::class, ['except' => ['destroy',] ]);
        $this->middleware(SuperAdmin::class, ['only' => ['destroy',] ]);

        $this->nationalitiesRepo = $nationalities;
        $this->statesRepo = $statesRepo;
        $this->townsRepo = $townsRepo;
        $this->qualificationRepo = $qualificationRepo;
        $this->programsRepo = $programsRepo;
        $this->studyModeRepo = $studyModeRepo;
        $this->intakeRepo = $intakeRepo;
        $this->user = $user;
        $this->studentRecordsRepo = $studentRecordsRepo;
        $this->userProgramsRepo = $userProgramsRepo;
        $this->userModesRepo = $userModesRepo;
        $this->userPaymentPlan = $userPaymentPlan;
    }
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['nationals'] = $this->nationalitiesRepo->getAll();
        $data['qualifications'] = $this->qualificationRepo->getAll();
        $data['studyMode'] = $this->studyModeRepo->getAll();
        $data['intake'] = $this->intakeRepo->getAll();
        $data['paymentPlanID'] = $this->userPaymentPlan->getAll();
        return view('pages.support_team.students.add',$data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $req)
    {
        $data =  $req->only(Qs::getUserRecord());
        $sr =  $req->only(Qs::getStudentData());
        $personInfo = $req->only(Qs::getUserPersonalinfor());
        $nextofkin = $req->only(Qs::getUserNKInfor());


        $data['user_type'] = 'student';
        $data['password'] = Hash::make('student');
        $data['photo'] = Qs::getDefaultUserImage();
        //prepare next kin
        $nextofkinp['full_name'] =  $nextofkin['nk_full_name'];
        $nextofkinp['tel'] = $nextofkin['nktel'];
        $nextofkinp['relationship'] = $nextofkin['nk_relationship'];;
        $nextofkinp['phone'] = $nextofkin['nk_phone'];
        $nextofkinp['city'] = $nextofkin['nk_town_id'];
        $nextofkinp['province'] = $nextofkin['nk_state_id'];
        $nextofkinp['country'] = $nextofkin['nk_nal_id'];

        if($req->hasFile('photo')) {
            $photo = $req->file('photo');
            $f = Qs::getFileMetaData($photo);
            $f['name'] = 'photo.' . $f['ext'];
            $f['path'] = $photo->storeAs(Qs::getUploadPath('student').$data['code'], $f['name']);
            $data['photo'] = asset('storage/' . $f['path']);
        }

        $user = $this->user->create($data); // Create User

        $personInfo['user_id'] = $user->id;
        $personInfo['dob'] = date('Y-m-d', strtotime($personInfo['dob']));
        $nextofkinp['user_id'] = $user->id;


        $this->studentRecordsRepo->createPIRecord($personInfo); // Add personal infor
        $this->studentRecordsRepo->createNKRecord($nextofkinp); // Add nextkin infor

        //$sr['adm_no'] = $data['username'];
        $srd['user_id'] = $user->id;
        $srd['intakeID'] = $sr['intakeID'];
        $srd['level_id'] = $sr['level_id'];
        $srd['typeID'] = $sr['typeID'];
        $srd['student_id'] = $this->generatestudentId();
        $srd['year_admitted'] = date("y");

        $upp['userID'] = $user->id;
        $upp['paymentPlanID'] = $sr['paymentPlanID'];
        $upp['key'] = $user->id.'-'.$sr['paymentPlanID'];

        $programd['userID'] = $user->id;
        $programd['programID'] = $sr['programID'];
        $programd['key'] = $sr['programID'].'-'.$user->id;
        $programd['activated_by'] =  Auth::user()->id;
        $umd['userID'] = $user->id;
        $umd['studyModeID'] = $sr['studymodeID'];
        //$sr['session'] = Qs::getSetting('current_session');

        $this->studentRecordsRepo->createRecord($srd); // Create Student
        $this->userProgramsRepo->create($programd); // Create Student program
        $this->userModesRepo->create($umd); // Create Student study mode
        $this->userPaymentPlan->create($upp);//create user payment plan

        return Qs::jsonStoreOk();

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data['nationals'] = $this->nationalitiesRepo->getAll();
        $data['studyMode'] = $this->studyModeRepo->getAll();
        $data['intake'] = $this->intakeRepo->getAll();
        return view('pages.support_team.students.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function getStates(string $id){
        return $this->statesRepo->getAlls($id);
    }
    public function getTowns(string $id){
        return $this->townsRepo->getAlls($id);
    }
    public function getPrograms($id){
        return $this->programsRepo->getAllProgramQualification($id);
    }
    public function getLevels($id){
        return \DB::table('ac_programs')
            ->where('ac_programs.id', '=', $id)
            ->join('ac_programCourses', 'ac_programCourses.programID', '=', 'ac_programs.id')
            ->join('ac_course_levels', 'ac_programCourses.level_id', '=', 'ac_course_levels.id')
            ->distinct()
            ->select('ac_course_levels.id as id', 'ac_course_levels.name as name')
            ->get();
        //return $this->programsRepo->getAllProgramQualification($id);
    }
    public function getStudents(){
        if (isset($_GET['query']) && $_GET['query']!==''){
            $searchText = $_GET['query'];
            $users['users'] = DB::table('users')->where('first_name','=','%'.$searchText.'%')
                ->orWhere('last_name','LIKE','%'.$searchText.'%')
                ->orWhere('student_id','LIKE','%'.$searchText.'%')
                ->orWhere('email','LIKE','%'.$searchText.'%')
                ->orWhere('nrc','LIKE','%'.$searchText.'%')
                ->orWhere('id','LIKE','%'.$searchText.'%')->get();
            return view('pages.support_team.students.list',$users);
        }else{
            return view('pages.support_team.students.list');
        }
           // return view('pages.support_team.students.list');
    }
    public function getStudentsSearch(Request $request){
        if (isset($request['query']) && !$request['query'] ==''){
            $searchText = $request['query'];
            $users['users'] = DB::table('users')->where('first_name','=','%'.$searchText.'%')
                ->orWhere('last_name','LIKE','%'.$searchText.'%')
                ->orWhere('student_id','LIKE','%'.$searchText.'%')
                ->orWhere('email','LIKE','%'.$searchText.'%')
                ->orWhere('nrc','LIKE','%'.$searchText.'%')
                ->orWhere('id','LIKE','%'.$searchText.'%')->get();

            return view('pages.support_team.students.list',$users);

        }else{
            return view('pages.support_team.students.list');
        }
    }
    private function generatestudentId(){
        $year    = date("y");
        // fetch lastid
        $lastID  = StudentRecord::get()->last();
        if ($lastID == null) {
            $finalID = "1";
        }else {
            $finalID = $lastID->id + 1;
        }
        $studentnumber =  $finalID;

        if ($studentnumber < 10) {
            $concat_studentnumber = "000" . $studentnumber;
        }
        elseif ($studentnumber > 99) {
            $concat_studentnumber = "0" . $studentnumber;
        }
        else {
            $concat_studentnumber = "00" . $studentnumber;
        }
        $month = date("m");

        if ($month <= 6) {
            $semester = 1;
        }
        else {
            $semester = 2;
        }
        return $year . $semester . $concat_studentnumber;
    }

}
