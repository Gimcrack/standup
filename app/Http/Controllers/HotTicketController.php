<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ingenious\Isupport\Facades\Isupport;

class HotTicketController extends Controller
{
    /**
     * Get an index of the resource
     * @method index
     *
     * @return   response()
     */
    public function index($groupOrIndividual = null, $id = null)
    {
        $data = Isupport::hot($groupOrIndividual, $id);

        return response()->json($data, 200);
    }
}
