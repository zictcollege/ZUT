<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Custom\SuperAdmin;
use App\Http\Middleware\Custom\TeamSA;
use App\Http\Requests\UserRequest;
use App\Repositories\NationalitiesRepo;
use App\Repositories\UserRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    protected $user,$nationalitiesRepo;

    public function __construct(UserRepo $user,NationalitiesRepo $nationalities)
    {
        $this->middleware(TeamSA::class, ['only' => ['index', 'store', 'edit', 'update'] ]);
        $this->middleware(SuperAdmin::class, ['only' => ['reset_pass','destroy'] ]);

        $this->user = $user;
        $this->nationalitiesRepo = $nationalities;
    }

    public function index()
    {
        $ut = $this->user->getAllTypes();
        $ut2 = $ut->where('level', '>', 2);

        $d['user_types'] = Qs::userIsAdmin() ? $ut2 : $ut;
        $d['users'] = $this->user->getPTAUsers();
        $d['nationals'] = $this->nationalitiesRepo->getAll();

        return view('pages.support_team.users.index', $d);
    }

    public function edit($id)
    {

    }

    public function update(UserRequest $req, $id)
    {

    }
    public function store(UserRequest $req){

        //$user_type = $this->user->findType($req->user_type)->title;

        $data =  $req->only(Qs::getUserRecords());
        $personInfo = $req->only(Qs::getUserPersonalinfor());

        //$data['user_type'] = $user_type;
        $data['password'] = Hash::make('test1234');
        $data['photo'] = Qs::getDefaultUserImage();


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

        $this->user->createPIRecord($personInfo); // Add personal infor

        return Qs::jsonStoreOk();
    }

    public function show($user_id)
    {
        $user_id = Qs::decodeHash($user_id);
        if(!$user_id){return back();}

        $data['user'] = $this->user->find($user_id);

        return view('pages.support_team.users.show', $data);
    }

    public function destroy($id)
    {

    }
}
