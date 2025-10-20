<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shift;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * 管理者専用ダッシュボード
     */
    public function dashboard()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'アクセス権限がありません');
        }

        return view('admin.dashboard');
    }

    /**
     * 会社情報登録・更新フォーム表示
     */
    public function showCompanyForm()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'アクセス権限がありません');
        }

        return view('admin.company');
    }

    /**
     * 会社情報更新処理
     */
    public function updateCompany(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'アクセス権限がありません');
        }

        $request->validate([
            'company_id' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->company_id = $request->company_id;
        $user->save();

        return redirect()->route('admin.dashboard')->with('message', '会社IDを更新しました');
    }

    /**
     * 所属会社のシフト一覧表示
     */
    public function showShifts()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'アクセス権限がありません');
        }

        $adminCompanyId = Auth::user()->company_id;

        // 自分と同じ会社に所属するユーザーのシフトだけ取得
        $shifts = Shift::with('user')
            ->whereHas('user', function ($query) use ($adminCompanyId) {
                $query->where('company_id', $adminCompanyId);
            })
            ->orderBy('date', 'asc')
            ->get();

        return view('admin.shifts', compact('shifts'));
    }
}
