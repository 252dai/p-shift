<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shift;
use Carbon\Carbon;
use App\Models\FixedShift;


class AdminCalendarController extends Controller
{
    public function index($year = null, $month = null)
    {
        $year = $year ?? Carbon::now()->year;
        $month = $month ?? Carbon::now()->month;

        $date = Carbon::createFromDate($year, $month, 1);

        // ユーザーの会社IDを取得
        $companyId = Auth::user()->company_id;

        // 該当月の希望シフトを取得
        $shifts = Shift::whereHas('user', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->with('user') // ← ユーザー情報をEager Load
            ->get();

        // 日付ごとにグループ化（Bladeで扱いやすくする）
        $groupedShifts = $shifts->groupBy('date');

        return view('admin.calendar_shift', [
            'date' => $date,
            'shifts' => $groupedShifts,
        ]);
    }

    public function fixShift(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        FixedShift::create([
            'user_id' => $request->user_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        

        return redirect()->route('admin.calendar.shift')->with('message', '確定シフトを登録しました');
    }
}
