<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FixedShift;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FixedShiftController extends Controller
{
    public function create()
    {
        $users = User::where('company_id', Auth::user()->company_id)
            ->where('role', 'user')
            ->get();
        return view('admin.fixed_create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        FixedShift::create($request->only('user_id', 'date', 'start_time', 'end_time'));

        return redirect()->route('fixed.create')->with('message', '確定シフトを登録しました');
    }

    public function userView()
    {
        $shifts = FixedShift::where('user_id', Auth::id())->orderBy('date')->get();
        return view('user.fixed_view', compact('shifts'));
    }

    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $yearMonth = $request->input('ym', Carbon::now()->format('Y-m'));
        [$year, $month] = explode('-', $yearMonth);

        $startDate = Carbon::create($year, $month, 1);
        $endDate = (clone $startDate)->endOfMonth();

        $shifts = FixedShift::with('user')
            ->whereHas('user', fn($q) => $q->where('company_id', $companyId))
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date')
            ->get();

        // 月の日数を取得
        $daysInMonth = $startDate->daysInMonth;

        // 日付ごとに確定シフトをまとめる（連想配列）
        $shiftsByDate = [];
        foreach ($shifts as $shift) {
            $shiftsByDate[$shift->date][] = $shift;
        }

        return view('admin.fixed_shifts_calendar', compact('yearMonth', 'startDate', 'daysInMonth', 'shiftsByDate'));
    }

    public function update(Request $request, $id)
    {
        $shift = FixedShift::findOrFail($id);

        $request->validate([
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        $shift->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('admin.fixed.index')->with('message', '確定シフトを更新しました');
    }

    public function destroy($id)
    {
        $shift = FixedShift::findOrFail($id);
        $shift->delete();

        return redirect()->route('admin.fixed.index')->with('message', '確定シフトを削除しました');
    }

    
}
