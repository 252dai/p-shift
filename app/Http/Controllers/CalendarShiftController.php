<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shift;
use Carbon\Carbon;

class CalendarShiftController extends Controller
{
    public function create($year = null, $month = null)
    {
        $year = $year ?? Carbon::now()->year;
        $month = $month ?? Carbon::now()->month;

        $date = Carbon::createFromDate($year, $month, 1);
        // 月末日までの日数など取得し、カレンダー表示用に渡す

        // その月のシフトを取得
        $shifts = Shift::where('user_id', Auth::id())
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        return view('user.calendar_shift', compact('date', 'shifts'));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();

        foreach ($request->input('shifts', []) as $date => $times) {
            if (!empty($times['start_time']) && !empty($times['end_time'])) {
                Shift::create([
                    'user_id' => $userId,
                    'date' => $date,
                    'start_time' => $times['start_time'],
                    'end_time' => $times['end_time'],
                ]);
            }
        }

        return redirect()->route('user.dashboard')->with('message', 'シフトを登録しました');
    }
}
