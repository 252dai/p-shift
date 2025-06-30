<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shift;
use Carbon\Carbon;

class ShiftEditController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        $shifts = Shift::where('user_id', $userId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get();

        return view('user.edit_shifts', compact('shifts'));
    }

    public function update(Request $request, $id)
    {
        $shift = Shift::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        $shift->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('user.shifts.edit')->with('message', '変更しました');
    }

    public function destroy($id)
    {
        $shift = Shift::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $shift->delete();

        return redirect()->route('user.shifts.edit')->with('message', '削除しました');
    }
}
