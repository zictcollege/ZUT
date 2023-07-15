<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Custom\SuperAdmin;
use App\Http\Middleware\Custom\TeamSA;
use App\Http\Requests\Fees\Fees;
use App\Http\Requests\Fees\FeesUpdate;
use App\Repositories\FeesRepo;
use Illuminate\Http\Request;

class FeesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $feesRepo;
    public function __construct(FeesRepo $feesRepo)
    {
        $this->middleware(TeamSA::class, ['except' => ['destroy',] ]);
        $this->middleware(SuperAdmin::class, ['only' => ['destroy',] ]);

        $this->feesRepo = $feesRepo;
    }
    public function index()
    {
        $fees['fees'] = $this->feesRepo->getAll();
        return view('pages.support_team.fees.index',$fees);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Fees $req)
    {
        $data = $req->only(['name']);
        $this->feesRepo->create($data);

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
        $fee['fee'] = $fees = $this->feesRepo->find($id);

        return !is_null($fees ) ? view('pages.support_team.fees.edit', $fee)
            : Qs::goWithDanger('fees.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FeesUpdate $req, string $id)
    {
        $id = Qs::decodeHash($id);
        $data = $req->only(['name']);
        $this->feesRepo->update($id, $data);

        return Qs::jsonUpdateOk();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->feesRepo->find($id)->delete();
        return back()->with('flash_success', __('msg.delete_ok'));
    }
}
