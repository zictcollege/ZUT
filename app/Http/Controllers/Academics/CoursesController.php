<?php

namespace App\Http\Controllers\Academics;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Custom\SuperAdmin;
use App\Http\Middleware\Custom\TeamSA;
use App\Http\Requests\Courses\Courses;
use App\Http\Requests\Courses\CoursesUpdate;
use App\Repositories\CoursesRepo;
use Illuminate\Http\Request;

class CoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $courses;
    public function __construct(CoursesRepo $courses)
    {
        $this->middleware(TeamSA::class, ['except' => ['destroy',] ]);
        $this->middleware(SuperAdmin::class, ['only' => ['destroy',] ]);

        $this->courses = $courses;
    }

    public function index()
    {
        $courses['courses'] = $this->courses->getAll();
        return view('pages.academics.courses.index',$courses);
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
    public function store(Courses $req)
    {
        $data = $req->only(['code', 'name']);
        $this->courses->create($data);

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
        $courses['course'] = $courses = $this->courses->find($id);

        return !is_null($courses ) ? view('pages.academics.courses.edit', $courses)
            : Qs::goWithDanger('pages.academics.courses.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CoursesUpdate $req, string $id)
    {
        $data = $req->only(['code','name']);
        $this->courses->update($id, $data);
        return Qs::jsonUpdateOk();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->courses->find($id)->delete();
        return back()->with('flash_success', __('msg.delete_ok'));
    }
}
