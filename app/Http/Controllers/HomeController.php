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
        $initial_state = Cache::remember('initial_state_' . \Auth::id(), 15, function() {
            $users = ( \Auth::user()->isAdmin() ) ? User::all() : [];
            $ticketsHot = Isupport::hot();
            $ticketsAging = Isupport::aging();
            $ticketsStale = Isupport::stale();
            $ticketsClosed = Isupport::recentClosed();
            $ticketsMine = Isupport::openTicketsByReps( [ \Auth::user()->name ] );
            $averageTimeOpen = Isupport::averageTimeOpen($resolution = 10, $groupOrIndividual = "Group", $id = "OIT", $years = 1);

            return collect(
                compact('users', 'ticketsHot', 'ticketsAging', 'ticketsStale',
                    'ticketsClosed', 'ticketsMine', 'averageTimeOpen')
            );
        });

        return view('home', compact('initial_state') );
    }
}
