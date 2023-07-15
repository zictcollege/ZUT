<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $d=[];
        //return view('home');
        return view('pages.support_team.dashboard', $d);
    }
    public function dashboard()
    {
        //return view('home');
        $d=[];
        //return view('home');
        return view('pages.support_team.dashboard', $d);
    }
}
