<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ingenious\Isupport\Facades\Isupport;

class ProblemTicketController extends Controller
{
    /**
     * Get an index of the resource
     * @method index
     *
     * @return   response()
     */
    public function index()
    {
        $data = Isupport::openTicketsByReps( request('reps') );

        return response()->json($data, 200);
    }
}


