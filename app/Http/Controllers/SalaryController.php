<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FixedShift;
use App\Models\User;
use Carbon\Carbon;

class SalaryController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;

        // 会社のユーザーを取得
        $users = User::where('company_id', $companyId)->get();

        $salaryData = [];

        foreach ($users as $user) {
            $shifts = FixedShift::where('user_id', $user->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get();

            $totalMinutes = 0;

            foreach ($shifts as $shift) {
                $start = strtotime($shift->start_time);
                $end = strtotime($shift->end_time);
                $diffMinutes = ($end - $start) / 60; // 分単位
                $totalMinutes += $diffMinutes;
            }

            $totalHours = $totalMinutes / 60;
            $salary = $totalHours * $user->hourly_wage;

            $salaryData[] = [
                'user' => $user,
                'total_hours' => round($totalHours, 2),
                'salary' => round($salary),
            ];
        }

        $totalSalary = array_sum(array_column($salaryData, 'salary'));

        return view('admin.salary.index', compact('salaryData', 'totalSalary', 'year', 'month'));
    }
}
