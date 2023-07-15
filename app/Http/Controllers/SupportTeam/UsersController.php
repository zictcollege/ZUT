<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Custom\SuperAdmin;
use App\Http\Middleware\Custom\TeamSA;
use App\Http\Requests\UserRequest;
use App\Repositories\UserRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    protected $user;

    public function __construct(UserRepo $user)
    {
        $this->middleware(TeamSA::class, ['only' => ['index', 'store', 'edit', 'update'] ]);
        $this->middleware(SuperAdmin::class, ['only' => ['reset_pass','destroy'] ]);

        $this->user = $user;
    }

    public function index()
    {
        $ut = $this->user->getAllTypes();
        $ut2 = $ut->where('level', '>', 2);

        $d['user_types'] = Qs::userIsAdmin() ? $ut2 : $ut;
        $d['users'] = $this->user->getPTAUsers();
        return view('pages.support_team.users.index', $d);
    }

    public function edit($id)
    {

    }

    public function update(UserRequest $req, $id)
    {

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
