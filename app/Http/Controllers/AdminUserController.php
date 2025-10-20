<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminUserController extends Controller
{
    /**
     * 管理者専用：会社に所属するユーザー一覧
     */
    public function index()
    {
        $admin = Auth::user();
        if ($admin->role !== 'admin') {
            abort(403, 'アクセス権限がありません');
        }

        $users = User::where('company_id', $admin->company_id)
                    ->where('role', 'user')
                    ->orderBy('name')
                    ->get();

        return view('admin.users', compact('users'));
    }

    /**
     * 管理者専用：ユーザー削除
     */
    public function destroy($id)
    {
        $admin = Auth::user();
        if ($admin->role !== 'admin') {
            abort(403, 'アクセス権限がありません');
        }

        $user = User::findOrFail($id);

        // 管理者を誤って削除しないようチェック
        if ($user->role === 'admin' || $user->company_id !== $admin->company_id) {
            abort(403, 'このユーザーは削除できません');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'ユーザーを削除しました');
    }

    /**
     * 管理者専用：ユーザー検索フォーム
     */
    public function searchForm()
    {
        $admin = Auth::user();
        if ($admin->role !== 'admin') {
            abort(403, 'アクセス権限がありません');
        }

        return view('admin.users.search');
    }

    /**
     * 管理者専用：ユーザー検索処理
     */
    public function search(Request $request)
    {
        $admin = Auth::user();
        if ($admin->role !== 'admin') {
            abort(403, 'アクセス権限がありません');
        }

        $request->validate([
            'keyword' => 'required|string|max:255',
        ]);

        $keyword = $request->keyword;

        $users = User::where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            })
            ->whereNull('company_id') // 会社に未所属のユーザーのみ
            ->get();

        return view('admin.users.search', compact('users', 'keyword'));
    }

    /**
     * 管理者専用：ユーザーを自社に招待（参加させる）
     */
    public function invite(User $user)
    {
        $admin = Auth::user();
        if ($admin->role !== 'admin') {
            abort(403, 'アクセス権限がありません');
        }

        // すでに会社に所属している場合は弾く
        if ($user->company_id) {
            return redirect()->back()->with('error', 'このユーザーはすでに会社に所属しています。');
        }

        // 管理者の会社に参加させる
        $user->company_id = $admin->company_id;
        $user->save();

        return redirect()->route('admin.users.searchForm')->with('success', 'ユーザーを会社に招待しました。');
    }
}
