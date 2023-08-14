<?php

namespace App\Repositories;

use App\Models\Admissions\StudentRecord;
use App\Models\Admissions\UserPersonalInformation;
use App\Models\Admissions\UsersNextOfKin;

class StudentRecords
{
    public function createRecord($data)
    {
        return StudentRecord::create($data);
    }
    public function createPIRecord($data)
    {
        return UserPersonalInformation::create($data);
    }
    public function createNKRecord($data)
    {
        return UsersNextOfKin::create($data);
    }

    public function getAll($order = 'id')
    {
        return StudentRecord::with('department','qualification')->orderBy($order)->get();
    }
    public function getAllwithOtherInfor($id,$order = 'id')
    {
        $recordId = StudentRecord::where('user_id','=',$id)->get();
        return StudentRecord::with('userinfo','intake','levels','periodType')->orderBy($order)->find($recordId[0]->id);
    }
    public function update($id, $data)
    {
        return StudentRecord::find($id)->update($data);
    }

    public function find($id)
    {
        return StudentRecord::find($id);
    }
}
