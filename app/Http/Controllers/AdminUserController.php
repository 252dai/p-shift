<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::where('company_id', Auth::user()->company_id)
                     ->where('role', 'user')
                     ->orderBy('name')
                     ->get();

        return view('admin.users', compact('users'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin' || $user->company_id !== Auth::user()->company_id) {
            abort(403, 'このユーザーは削除できません');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('message', 'ユーザーを削除しました');
    }

    public function searchForm()
{
    return view('admin.users.search');
}

    public function search(Request $request)
{
    $request->validate([
        'keyword' => 'required|string|max:255',
    ]);

    $keyword = $request->keyword;

    $users = User::where(function ($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('email', 'like', "%{$keyword}%");
        })
        ->whereNull('company_id') // 会社にまだ所属していないユーザーのみ
        ->get();

    return view('admin.users.search', compact('users', 'keyword'));
}

    public function invite(User $user)
{
    $admin = auth()->user();

    // 管理者でなければ403エラー
    if ($admin->role !== 'admin') {
        abort(403);
    }

    // 既に会社に所属しているかチェック
    if ($user->company_id) {
        return redirect()->back()->with('error', 'このユーザーはすでに会社に所属しています。');
    }

    // 管理者の会社IDをユーザーにセット
    $user->company_id = $admin->company_id;
    $user->save();

    return redirect()->route('admin.users.searchForm')->with('success', 'ユーザーを招待しました。');
}

}
