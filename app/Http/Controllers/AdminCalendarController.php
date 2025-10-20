<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shift;
use App\Models\FixedShift;
use Carbon\Carbon;

class AdminCalendarController extends Controller
{
    /**
     * 管理者用：希望シフト一覧をカレンダー形式で表示
     */
    public function index($year = null, $month = null)
    {
        // 🔒 管理者以外はアクセス拒否
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, '管理者専用ページです');
        }

        // 年月を指定または現在日時で取得
        $year = $year ?? Carbon::now()->year;
        $month = $month ?? Carbon::now()->month;

        $date = Carbon::createFromDate($year, $month, 1);
        $companyId = Auth::user()->company_id;

        // 同じ会社のユーザーの希望シフト取得
        $shifts = Shift::whereHas('user', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->with('user')
            ->get();

        // 日付ごとにまとめる
        $groupedShifts = $shifts->groupBy('date');

        return view('admin.calendar_shift', [
            'date' => $date,
            'shifts' => $groupedShifts,
        ]);
    }

    /**
     * 管理者用：希望シフトから確定シフトを登録
     */
    public function fixShift(Request $request)
    {
        // 🔒 管理者以外は拒否
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'アクセス権限がありません');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        // すでに確定シフトがある場合はエラー
        $exists = FixedShift::where('user_id', $request->user_id)
            ->where('date', $request->date)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'このユーザーの確定シフトはすでに登録されています。');
        }

        FixedShift::create([
            'user_id' => $request->user_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('admin.calendar.shift')->with('message', '確定シフトを登録しました');
    }
}
