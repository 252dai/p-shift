<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Shift;
use App\Models\User;
use App\Models\FixedShift;

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

    /**
     * 一括確定処理
     */
    public function bulkFix(Request $request)
    {
        try {
            // 管理者権限チェック
            if (auth()->user()->role !== 'admin') {
                return redirect()->back()->with('error', 'アクセス権限がありません');
            }

            // JSONデータをデコード
            $shifts = json_decode($request->shifts, true);
            
            // デバッグログ
            Log::info('一括確定開始', ['shifts_count' => count($shifts), 'data' => $shifts]);
            
            if (empty($shifts)) {
                return redirect()->back()->with('error', 'シフトが選択されていません');
            }
            
            $successCount = 0;
            $errorCount = 0;
            $adminCompanyId = auth()->user()->company_id;
            
            foreach ($shifts as $shift) {
                try {
                    // 必須フィールドのチェック
                    if (!isset($shift['user_id']) || !isset($shift['date']) || 
                        !isset($shift['start_time']) || !isset($shift['end_time'])) {
                        Log::warning('必須フィールドが不足', ['shift' => $shift]);
                        $errorCount++;
                        continue;
                    }

                    // ユーザーが同じ会社に所属しているか確認
                    $user = User::find($shift['user_id']);
                    if (!$user || $user->company_id !== $adminCompanyId) {
                        Log::warning('ユーザーが見つからないか会社が異なる', ['user_id' => $shift['user_id']]);
                        $errorCount++;
                        continue;
                    }

                    // 既に確定済みのシフトがないかチェック（重複防止）
                    $existingShift = FixedShift::where('user_id', $shift['user_id'])
                        ->where('date', $shift['date'])
                        ->first();
                    
                    if ($existingShift) {
                        Log::info('既に確定済み', ['shift' => $shift]);
                        $errorCount++;
                        continue;
                    }

                    // fixed_shifts テーブルに保存
                    FixedShift::create([
                        'user_id' => $shift['user_id'],
                        'date' => $shift['date'],
                        'start_time' => $shift['start_time'],
                        'end_time' => $shift['end_time'],
                        'company_id' => $adminCompanyId,
                    ]);
                    
                    $successCount++;
                    Log::info('シフト確定成功', ['shift' => $shift]);
                    
                } catch (\Exception $e) {
                    Log::error('個別シフト確定エラー', [
                        'shift' => $shift,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    $errorCount++;
                }
            }
            
            // 結果のログ
            Log::info('一括確定完了', [
                'success' => $successCount,
                'error' => $errorCount
            ]);
            
            if ($successCount > 0) {
                $message = "{$successCount}件のシフトを確定しました";
                if ($errorCount > 0) {
                    $message .= "（{$errorCount}件はエラーのためスキップされました）";
                }
                return redirect()->back()->with('success', $message);
            } else {
                return redirect()->back()->with('error', 'シフトの確定に失敗しました。ログを確認してください。');
            }
            
        } catch (\Exception $e) {
            Log::error('一括確定エラー', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return redirect()->back()->with('error', '確定処理中にエラーが発生しました: ' . $e->getMessage());
        }
    }
}