<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Custom\SuperAdmin;
use App\Http\Middleware\Custom\TeamSA;
use App\Http\Requests\PeriodTypes\PeriodType;
use App\Http\Requests\PeriodTypes\PeriodTypeUpdate;
use App\Repositories\PeriodTypeRepo;

class PeriodTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $periodTypes;
    public function __construct(PeriodTypeRepo $periodTypes)
    {
        $this->middleware(TeamSA::class, ['except' => ['destroy',] ]);
        $this->middleware(SuperAdmin::class, ['only' => ['destroy',] ]);


        $this->periodTypes = $periodTypes;
    }

    public function index()
    {
        $types['type'] = $this->periodTypes->getAll();
        return view('pages.support_team.academicperiodtypes.index',$types);
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
    public function store(PeriodType $req)
    {
        $data = $req->only(['name', 'description']);

        $this->periodTypes->create($data);

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
        $type['type'] = $periodtypes = $this->periodTypes->find($id);

        return !is_null($periodtypes ) ? view('pages.support_team.academicperiodtypes.edit', $type)
            : Qs::goWithDanger('pages.support_team.academicperiodtypes.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PeriodTypeUpdate $req, string $id)
    {
        //$data = $req->only(['name', 'description']);
        $data = $req->only(['name']);
        $this->periodTypes->update($id, $data);

        return Qs::jsonUpdateOk();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->periodTypes->find($id)->delete();
        return back()->with('flash_success', __('msg.delete_ok'));
    }
}
