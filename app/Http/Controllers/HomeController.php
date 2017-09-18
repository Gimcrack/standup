<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        $initial_state = Cache::remember('initial_state', 15, function() {
            $users = ( \Auth::user()->isAdmin() ) ? User::all() : [];
            $ticketsHot = Isupport::hot();
            $ticketsAging = Isupport::aging();
            $ticketsStale = Isupport::stale();
            $ticketsClosed = Isupport::recentClosed();
            $ticketsMine = Isupport::openTicketsByReps( [ \Auth::user()->name ] );

            return collect(compact('users', 'ticketsHot', 'ticketsAging', 'ticketsStale','ticketsClosed', 'ticketsMine'));
        });



        return view('home', compact('initial_state') );
    }
}
