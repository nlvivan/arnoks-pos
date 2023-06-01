<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentDate = Carbon::now();
        $todaySales = Transaction::whereDate('created_at', $currentDate->format('Y-m-d'))->sum('total_amount');
        $thisWeekSales = Transaction::whereBetween('created_at', [$currentDate->startOfWeek()->format('Y-m-d'), $currentDate->endOfWeek()->format('Y-m-d')])->sum('total_amount');
        $thisMontSales = Transaction::whereBetween('created_at', [$currentDate->startOfMonth()->format('Y-m-d'), $currentDate->endOfMonth()->format('Y-m-d')])->sum('total_amount');

        $transactions = Transaction::query()
                        ->when($request->from_date && $request->to_date, function ($query) use ($request) {
                            $query->whereBetween('created_at', [Carbon::parse($request->from_date)->startOfDay(), Carbon::parse($request->to_date)->startOfDay()]);
                        }, function ($query) {
                            $query->whereDate('created_at', Carbon::today());
                        })->paginate();

        return Inertia::render('Dashboard', [
            'todaySales' => $todaySales,
            'weekSales' => $thisWeekSales,
            'monthSales' => $thisMontSales,
            'transactions' => $transactions,
            'searchProps' => $request->only(['from_date', 'to_date']),
        ]);
    }
}
