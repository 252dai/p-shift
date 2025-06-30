<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shift;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function showCompanyForm()
    {
        return view('admin.company');
    }

    public function updateCompany(Request $request)
    {
        $request->validate([
            'company_id' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->company_id = $request->company_id;
        $user->save();

        return redirect()->route('admin.dashboard')->with('message', '会社IDを更新しました');
    }

    public function showShifts()
    {
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
