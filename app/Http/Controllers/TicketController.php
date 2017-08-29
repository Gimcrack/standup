<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Ingenious\Isupport\Facades\Isupport;

class TicketController extends Controller
{
    /**
     * Get an index of the resource
     * @method index
     *
     * @return   response()
     */
    public function index($groupOrIndividual = null, $id = null)
    {
        $data = Isupport::tickets($groupOrIndividual, $id);

        return response()->json($data, 200);
    }
}
