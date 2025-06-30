<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;

        // 同じ会社IDのユーザーのチャットだけ取得
        $chats = Chat::with('user')
            ->whereHas('user', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('chat.index', compact('chats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        Chat::create([
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return redirect()->route('chat.index');
    }
}
