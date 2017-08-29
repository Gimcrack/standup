<?php

namespace App\Http\Controllers;

use App\LogEntry;
use Illuminate\Http\Request;

class LogEntryController extends Controller
{
    /**
     * Get a listing of the resource
     * @method index
     *
     * @return   response
     */
    public function index()
    {
        return response()->json( LogEntry::with('client')->latest()->paginate(25), 200);
    }

    /**
     * Get a single log entry
     * @method show
     *
     * @return   response
     */
    public function show(LogEntry $log)
    {
        $log->load('client');
        return response()->json( $log, 200);
    }
}
