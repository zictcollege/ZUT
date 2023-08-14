<?php

namespace App\Http\Controllers;

use App\Http\Middleware\Custom\Student;
use App\Http\Middleware\Custom\SuperAdmin;
use App\Http\Middleware\Custom\TeamSA;
use App\Http\Requests\UserChangePass;
use App\Repositories\StudentRecords;
use App\Repositories\UserRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MyAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $studentrecordsRepo,$userRepo;

    public function __construct(StudentRecords $studentrecordsRepo,UserRepo $userRepo)
    {


        $this->studentrecordsRepo = $studentrecordsRepo;
         $this->userRepo = $userRepo;
    }
    public function index()
    {
        $d = Auth::user();
        $data = $this->userRepo->find($d->id);
        return view('pages.support_team.my_account', compact('data'));
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
    public function store(Request $request)
    {
        //
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
        //
    }
    public function change_pass(UserChangePass $req)
    {
        $user_id = Auth::user()->id;
        $my_pass = Auth::user()->password;
        $old_pass = $req->current_password;
        $new_pass = $req->password;

        if(password_verify($old_pass, $my_pass)){
            $data['password'] = Hash::make($new_pass);
            $this->userRepo->update($user_id, $data);
            return back()->with('flash_success', __('msg.p_reset'));
        }

        return back()->with('flash_danger', __('msg.p_reset_fail'));
    }
}
