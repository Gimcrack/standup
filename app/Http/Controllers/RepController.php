<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ingenious\Isupport\Facades\Isupport;

class RepController extends Controller
{
    /**
     * Get an index of the resource
     * @method index
     *
     * @return   reponse()
     */
    public function index()
    {
        return response()->json( [
            'errors' => false] +
            Isupport::reps()
        , 200 );
    }
}
