<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GenerateReportController extends Controller
{
    public function generate(Request $request)
    {
       $currentDate = Carbon::now();

        return (new ReportExport($request->from_date, $request->to_date))->download($currentDate->format('Y-m-d').'-transactionss.xlsx');
    }
}
