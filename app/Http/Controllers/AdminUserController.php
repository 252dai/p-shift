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
}