<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ingenious\Isupport\Facades\Isupport;

class TicketMetricsController extends Controller
{
    /**
     * Get the average time open for archive tickets
     * @method averageTimeOpen
     *
     * @return   response
     */
    public function averageTimeOpen($groupOrIndividual =null, $id = null, $resolution = 10, $years = 2)
    {
        $data = Isupport::averageTimeOpen($resolution, $groupOrIndividual, $id, $years);

        return response()->json($data, 200);
    }
}
