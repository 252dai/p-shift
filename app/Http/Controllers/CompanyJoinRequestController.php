<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanyJoinRequest;

class CompanyJoinRequestController extends Controller
{
    public function create()
    {
        $request = CompanyJoinRequest::where('user_id', Auth::id())->latest()->first();
        return view('user.company_request', compact('request'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|string',
        ]);

        CompanyJoinRequest::create([
            'user_id' => Auth::id(),
            'target_company_id' => $request->company_id,
            'status' => 'pending',
        ]);

        return redirect()->route('user.company.request')->with('message', '会社への参加申請を送信しました。承認をお待ちください。');
    }
}
