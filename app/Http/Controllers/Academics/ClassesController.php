<?php

namespace App\Http\Controllers\Academics;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Custom\SuperAdmin;
use App\Http\Middleware\Custom\TeamSA;
use App\Http\Requests\Classes\Classes;
use App\Repositories\Classesrepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class ClassesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $classrepo;

    public function __construct(Classesrepo $classesrepo)
    {
        $this->middleware(TeamSA::class, ['except' => ['destroy',] ]);
        $this->middleware(SuperAdmin::class, ['only' => ['destroy',] ]);

        $this->classrepo = $classesrepo;
        // $this->user = $user;
    }
    public function index()
    {
        return view('pages.academics.classes.index');
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
    public function store(Classes $req)
    {
        $data = $req->only(['instructorID', 'courseID', 'academicPeriodID']);

        $validator = Validator::make($data, [
            'instructorID' => 'required',
            'courseID' => 'required',
            'academicPeriodID' => 'required',
        ]);

        if ($validator->fails()) {
            return false;
        }

        $instructorID = $req->input('instructorID');
        $courseID = $req->input('courseID');
        $academicPeriodID = $req->input('academicPeriodID');

        $exists = DB::table('ac_classes')
            ->where('instructorID', $instructorID)
            ->where('courseID', $courseID)
            ->where('academicPeriodID', $academicPeriodID)
            ->exists();

        if ($exists) {
            $validator->errors()->add('instructorID', 'The combination of instructorID, courseID, and academicPeriodID already exists.');
            return false;
        }

        $this->classrepo->create($data);
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
        $this->classrepo->find($id)->delete();
        return back()->with('flash_success', __('msg.delete_ok'));
    }
}
