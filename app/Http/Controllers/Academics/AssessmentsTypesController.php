<?php

namespace App\Http\Controllers\Academics;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Custom\SuperAdmin;
use App\Http\Middleware\Custom\TeamSA;
use App\Http\Requests\AssessmentTypes\Assessments;
use App\Http\Requests\AssessmentTypes\AssessmentsUpdate;
use App\Models\Results\ImportList;
use App\Repositories\AssessmentTypesRepo;
use Illuminate\Http\Request;

class AssessmentsTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $assessmentRepo;
    public function __construct(AssessmentTypesRepo $assessmentTypesRepo)
    {
        $this->middleware(TeamSA::class, ['except' => ['destroy',] ]);
        $this->middleware(SuperAdmin::class, ['only' => ['destroy',] ]);

        $this->assessmentRepo = $assessmentTypesRepo;
    }

    public function index()
    {
        $assessment['assessments'] = $this->assessmentRepo->getAll();
        return view('pages.academics.assessment_types.index',$assessment);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Assessments $req)
    {
        $data = $req->only(['name']);
        $this->assessmentRepo->create($data);

        return Qs::jsonStoreOk();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $assessment['assessment'] = $courses = $this->assessmentRepo->find($id);

        return !is_null($courses ) ? view('pages.academics.assessment_types.edit', $assessment)
            : Qs::goWithDanger('pages.academics.assessment_types.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AssessmentsUpdate $req, string $id)
    {
        $data = $req->only(['name']);
        $this->assessmentRepo->update($id, $data);
        return Qs::jsonUpdateOk();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //check if assessment is already in the DB
        $available = ImportList::where('assessmentID',$id)->get();

        if ($available && count($available)>0){
            return back()->with('flash_danger', __('msg.delete_not_okay'));
        }else{
            $this->assessmentRepo->find($id)->delete();
            return back()->with('flash_success', __('msg.delete_ok'));
        }
    }
}
