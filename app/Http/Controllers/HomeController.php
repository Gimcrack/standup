<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Definitions\Facades\Definitions;
use Ingenious\Isupport\Facades\Isupport;

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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // initial state

        $users = User::all();
        $ticketsHot = Isupport::hot();
        $ticketsAging = Isupport::aging();
        $ticketsStale = Isupport::stale();
        $ticketsClosed = Isupport::recentClosed();

        $initial_state = collect(compact('users', 'ticketsHot', 'ticketsAging', 'ticketsStale','ticketsClosed'));

        return view('home', compact('initial_state') );
    }
}
