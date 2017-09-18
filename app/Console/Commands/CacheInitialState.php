<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Support\Facades\Cache;
use Ingenious\Isupport\Facades\Isupport;
use Illuminate\Console\Command;

class CacheInitialState extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrum:cacheInitialState';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the initial state of the app';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
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
    }
}
