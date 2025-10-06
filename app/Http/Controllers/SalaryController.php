<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FixedShift;
use App\Models\User;
use Carbon\Carbon;

class SalaryController extends Controller
{
    /**
     * 管理者用：会社内全ユーザーの給与一覧
     */
    public function index()
    {
        $companyId = Auth::user()->company_id;
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;

        $users = User::where('company_id', $companyId)->get();
        $salaryData = [];

        foreach ($users as $user) {
            $shifts = FixedShift::where('user_id', $user->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get();

            $totals = $this->calculateShiftTotals($shifts, $user->hourly_wage);

            $salaryData[] = array_merge(['user' => $user], $totals);
        }

        $totalSalary = array_sum(array_column($salaryData, 'salary'));

        return view('admin.salary.index', [
            'salaryData' => $salaryData,
            'totalSalary' => $totalSalary,
            'year' => $year,
            'month' => $month,
        ]);
    }

    /**
     * 管理者用：時給を更新
     */
    public function updateHourlyWage(Request $request, User $user)
    {
        $request->validate([
            'hourly_wage' => 'required|numeric|min:0',
        ]);

        $user->hourly_wage = $request->hourly_wage;
        $user->save();

        return redirect()->back()->with('success', '時給を更新しました');
    }

    /**
     * ユーザー用：自分の給与確認
     */
    public function userIndex()
    {
        $user = Auth::user();
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;

        $shifts = FixedShift::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $totals = $this->calculateShiftTotals($shifts, $user->hourly_wage);

        return view('user.salary.index', array_merge([
            'user' => $user,
            'year' => $year,
            'month' => $month,
        ], $totals));
    }

    /**
     * 勤務シフトをもとに時間と給与を計算
     *
     * @param \Illuminate\Support\Collection $shifts
     * @param float $hourlyWage
     * @return array
     */
    private function calculateShiftTotals($shifts, $hourlyWage)
    {
        $totalRegularMinutes = 0;
        $totalOvertimeMinutes = 0;
        $totalNightMinutes = 0;

        foreach ($shifts as $shift) {
            $start = strtotime($shift->start_time);
            $end = strtotime($shift->end_time);
            $diffMinutes = ($end - $start) / 60;

            // 基本8時間を超えた分は残業
            $regularMinutes = min($diffMinutes, 8*60);
            $overtimeMinutes = max($diffMinutes - 8*60, 0);

            // 深夜時間（22:00〜翌5:00）を1分単位で計算
            $nightMinutes = 0;
            $current = $start;
            while ($current < $end) {
                $hour = (int)date('H', $current);
                $minute = (int)date('i', $current);
                $timeInMinutes = $hour*60 + $minute;

                if ($timeInMinutes >= 22*60 || $timeInMinutes < 5*60) {
                    $nightMinutes++;
                }
                $current += 60; // 1分刻み
            }

            // 残業と深夜が重ならないように調整
            $overtimeMinutes = max($overtimeMinutes - $nightMinutes, 0);

            $totalRegularMinutes += $regularMinutes;
            $totalOvertimeMinutes += $overtimeMinutes;
            $totalNightMinutes += $nightMinutes;
        }

        $regularHours = $totalRegularMinutes / 60;
        $overtimeHours = $totalOvertimeMinutes / 60;
        $nightHours = $totalNightMinutes / 60;

        $salary = $regularHours * $hourlyWage
                + $overtimeHours * $hourlyWage * 1.25
                + $nightHours * $hourlyWage * 1.25;

        return [
            'total_hours' => round($regularHours + $overtimeHours + $nightHours, 2),
            'regular_hours' => round($regularHours, 2),
            'overtime_hours' => round($overtimeHours, 2),
            'night_hours' => round($nightHours, 2),
            'salary' => round($salary),
        ];
    }
}
