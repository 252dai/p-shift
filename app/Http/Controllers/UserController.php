<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Shift;


class UserController extends Controller
{
    public function dashboard()
    {
        return view('user.dashboard');
    }

    public function showCompanyJoinForm()
    {
        return view('user.company');
    }

    public function joinCompany(Request $request)
    {
        $request->validate([
            'company_id' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->company_id = $request->company_id;
        $user->save();

        return redirect()->route('user.dashboard')->with('message', '会社に参加しました');
    }

    public function edit($year = null, $month = null)
    {
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
