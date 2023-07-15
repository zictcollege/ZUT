<?php

namespace App\Http\Controllers\Academics;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Custom\SuperAdmin;
use App\Http\Middleware\Custom\TeamSA;
use App\Http\Requests\departments\Department;
use App\Http\Requests\departments\DepartmentUpdate;
use App\Repositories\DepartmentsRepo;
use App\Repositories\ProgramCoursesRepo;
use App\Repositories\ProgramsRepo;
use Illuminate\Http\Request;

class DepartmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $department;
    public function __construct(DepartmentsRepo $department)
    {
        $this->middleware(TeamSA::class, ['except' => ['destroy',] ]);
        $this->middleware(SuperAdmin::class, ['only' => ['destroy',] ]);


        $this->department = $department;
    }
    public function index()
    {
        $departments['departments'] = $this->department->getAll();
        return view('pages.academics.departments.index',$departments);
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
    public function store(Department $req)
    {

        if($req->hasFile('cover')) {
            $data = $req->only(['name', 'description','cover']);
            $logo = $req->file('cover');
            $f = Qs::getFileMetaData($logo);
            $f['name'] = $data['name'].'logo.' . $f['ext'];
            $f['path'] = $logo->storeAs(Qs::getPublicUploadPathDep(), $f['name']);
            $logo_path = asset('storage/depart/' . $f['name']);
            $data['cover'] = $logo_path;
            $this->department->create($data);
        }else{
            $data = $req->only(['name', 'description']);
            $this->department->create($data);
        }

        return Qs::jsonStoreOk();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data['departments'] = $department = $this->department->find($id);
        return !is_null($department ) ? view('pages.academics.departments.show',$data)
            : Qs::goWithDanger('pages.academics.departments.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['departments'] = $department = $this->department->find($id);
        return !is_null($department ) ? view('pages.academics.departments.edit',$data)
            : Qs::goWithDanger('pages.academics.departments.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DepartmentUpdate $req, string $id)
    {
        if($req->hasFile('cover')) {
            $data = $req->only(['name', 'description','cover']);
            $logo = $req->file('cover');
            $f = Qs::getFileMetaData($logo);
            $f['name'] = $data['name'].'logo.' . $f['ext'];
            $f['path'] = $logo->storeAs(Qs::getPublicUploadPathDep(), $f['name']);
            $logo_path = asset('storage/depart/' . $f['name']);
            $data['cover'] = $logo_path;
            $this->department->update($id,$data);
        }else{
            $data = $req->only(['name', 'description']);
            $this->department->update($id,$data);
        }

        return Qs::jsonStoreOk();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->department->find($id)->delete();
        return back()->with('flash_success', __('msg.delete_ok'));
    }
}
