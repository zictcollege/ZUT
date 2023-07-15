<?php

namespace App\Http\Controllers\Academics;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Custom\SuperAdmin;
use App\Http\Middleware\Custom\TeamSA;
use App\Http\Requests\Prerequisites\Prerequisite;
use App\Http\Requests\Prerequisites\PrerequisiteUpdate;
use App\Repositories\CoursesRepo;
use App\Repositories\PrerequisiteRepo;
use Illuminate\Http\Request;

class PrerequisiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $prerequisiteRepo,$coursesRepo;
    public function __construct(PrerequisiteRepo $prerequisiteRepo,CoursesRepo $coursesRepo)
    {
        $this->middleware(TeamSA::class, ['except' => ['destroy',]]);
        $this->middleware(SuperAdmin::class, ['only' => ['destroy',]]);

        $this->prerequisiteRepo = $prerequisiteRepo;
        $this->coursesRepo = $coursesRepo;
    }

    public function index()
    {
        $courses['courses'] = $this->prerequisiteRepo->getAll();
        $courses['pcourses'] = $this->coursesRepo->getAll();
        return view('pages.academics.prerequisites.index',$courses);
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
    public function store(Prerequisite $req)
    {
        $data = $req->only(['courseID', 'prerequisiteID']);

        foreach ($data['prerequisiteID'] as $courseID) {
            $this->prerequisiteRepo->create([
                'prerequisiteID' => $courseID,
                'courseID' => $data['courseID'],
            ]);
        }

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
        $id = Qs::decodeHash($id);
        $courses['courses'] = $this->prerequisiteRepo->find($id);
        $courses['pcourses'] = $this->coursesRepo->getAll();
        return view('pages.academics.prerequisites.edit',$courses);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PrerequisiteUpdate $req, string $id)
    {
        $id = Qs::decodeHash($id);
        $data = $req->only(['courseID', 'prerequisiteID']);

        foreach ($data['prerequisiteID'] as $courseID) {
            $this->prerequisiteRepo->update($id,[
                'prerequisiteID' => $courseID,
                'courseID' => $data['courseID'],
            ]);
        }
        return Qs::jsonUpdateOk();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = Qs::decodeHash($id);
        $this->prerequisiteRepo->findone($id)->delete();
        return back()->with('flash_success', __('msg.delete_ok'));
    }
}
