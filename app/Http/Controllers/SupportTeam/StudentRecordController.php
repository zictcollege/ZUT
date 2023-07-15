<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use App\Http\Middleware\Custom\SuperAdmin;
use App\Http\Middleware\Custom\TeamSA;
use App\Models\Nationalities;
use App\Repositories\IntakesRepo;
use App\Repositories\NationalitiesRepo;
use App\Repositories\ProgramsRepo;
use App\Repositories\QualificationsRepo;
use App\Repositories\StatesRepo;
use App\Repositories\StudyModeRepo;
use App\Repositories\TownsRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $statesRepo,$townsRepo,$nationalitiesRepo,$qualificationRepo,$programsRepo,
                $studyModeRepo,$intakeRepo,$typeRepo;

    public function __construct(NationalitiesRepo $nationalities,StatesRepo $statesRepo,TownsRepo $townsRepo,
    QualificationsRepo $qualificationRepo,ProgramsRepo $programsRepo,StudyModeRepo $studyModeRepo,IntakesRepo $intakeRepo)
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
        return view('pages.support_team.students.add',$data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
}
