<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FixedShift;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FixedShiftController extends Controller
{
    /**
     * 管理者：確定シフト作成フォームを表示
     */
    public function create()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, '管理者専用ページです');
        }

        $users = User::where('company_id', Auth::user()->company_id)
            ->where('role', 'user')
            ->get();

        return view('admin.fixed_create', compact('users'));
    }

    /**
     * 管理者：確定シフト登録処理
     */
    public function store(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, '管理者専用ページです');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        FixedShift::create($request->only('user_id', 'date', 'start_time', 'end_time'));

        return redirect()->route('fixed.create')->with('message', '確定シフトを登録しました');
    }

    /**
     * 一般ユーザー：自分の確定シフト一覧を表示
     */
    public function userView()
    {
        if (!Auth::check() || Auth::user()->role !== 'user') {
            abort(403, '一般ユーザー専用ページです');
        }

        $shifts = FixedShift::where('user_id', Auth::id())->orderBy('date')->get();
        return view('user.fixed_view', compact('shifts'));
    }

    /**
     * 管理者：確定シフト一覧（カレンダー形式）
     */
    public function index(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, '管理者専用ページです');
        }

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

        $daysInMonth = $startDate->daysInMonth;
        $shiftsByDate = [];

        foreach ($shifts as $shift) {
            $shiftsByDate[$shift->date][] = $shift;
        }

        return view('admin.fixed_shifts_calendar', compact('yearMonth', 'startDate', 'daysInMonth', 'shiftsByDate'));
    }

    /**
     * 管理者：確定シフト編集画面
     */
    public function edit($id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, '管理者専用ページです');
        }

        $shift = FixedShift::with('user')->findOrFail($id);
        return view('admin.fixed_edit', compact('shift'));
    }

    /**
     * 管理者：確定シフト更新
     */
    public function update(Request $request, $id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, '管理者専用ページです');
        }

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

    /**
     * 管理者：確定シフト削除
     */
    public function destroy($id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'アクセス権限がありません');
        }

        $shift = FixedShift::findOrFail($id);
        $shift->delete();

        return redirect()->route('admin.fixed.index')->with('message', '確定シフトを削除しました');
    }
}
