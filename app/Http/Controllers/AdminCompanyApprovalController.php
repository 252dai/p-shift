<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanyJoinRequest;
use App\Models\User;

class AdminCompanyApprovalController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        $requests = CompanyJoinRequest::where('target_company_id', $companyId)
            ->where('status', 'pending')
            ->with('user')
            ->get();

        return view('admin.company_requests', compact('requests'));
    }

    public function approve($id)
    {
        $request = CompanyJoinRequest::findOrFail($id);
        $user = $request->user;
        $user->company_id = $request->target_company_id;
        $user->save();

        $request->status = 'approved';
        $request->save();

        return redirect()->route('admin.company.requests')->with('message', '承認しました');
    }

    public function reject($id)
    {
        $request = CompanyJoinRequest::findOrFail($id);
        $request->status = 'rejected';
        $request->save();

        return redirect()->route('admin.company.requests')->with('message', '拒否しました');
    }
}
