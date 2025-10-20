<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Shift;

class UserController extends Controller
{
    /**
     * ユーザー専用ダッシュボード
     */
    public function dashboard()
    {
        if (auth()->user()->role !== 'user') {
            abort(403, 'アクセス権限がありません');
        }

        return view('user.dashboard');
    }

    /**
     * 会社参加申請フォーム表示
     */
    public function showCompanyJoinForm()
    {
        if (auth()->user()->role !== 'user') {
            abort(403, 'アクセス権限がありません');
        }

        return view('user.company');
    }

    /**
     * 会社参加申請処理
     */
    public function joinCompany(Request $request)
    {
        if (auth()->user()->role !== 'user') {
            abort(403, 'アクセス権限がありません');
        }

        $request->validate([
            'company_id' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->company_id = $request->company_id;
        $user->save();

        return redirect()->route('user.dashboard')->with('message', '会社に参加しました');
    }

    /**
     * 提出済みシフトの編集画面
     */
    public function edit($year = null, $month = null)
    {
        if (auth()->user()->role !== 'user') {
            abort(403, 'アクセス権限がありません');
        }

        $year = $year ?? Carbon::now()->year;
        $month = $month ?? Carbon::now()->month;

        $date = Carbon::createFromDate($year, $month, 1);

        $shifts = Shift::where('user_id', Auth::id())
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->keyBy(function ($shift) {
                return Carbon::parse($shift->date)->format('Y-m-d');
            });

        return view('user.edit_shifts', compact('shifts', 'date'));
    }
}
