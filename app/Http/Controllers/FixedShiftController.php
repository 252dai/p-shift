<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FixedShift;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FixedShiftController extends Controller
{
    /**
     * 確定シフト作成フォームを表示する
     * 管理者が所属する会社の一般ユーザー一覧を取得してフォームに渡す
     */
    public function create()
    {
        // ログイン管理者の会社IDに属するroleが'user'のユーザーを取得
        $users = User::where('company_id', Auth::user()->company_id)
            ->where('role', 'user')
            ->get();

        // 確定シフト作成フォームのビューにユーザー一覧を渡す
        return view('admin.fixed_create', compact('users'));
    }

    /**
     * 確定シフトの新規登録処理
     * バリデーションを行い、問題なければDBに保存する
     */
    public function store(Request $request)
    {
        // 入力内容を検証
        $request->validate([
            'user_id' => 'required|exists:users,id', // ユーザーIDがDBに存在すること
            'date' => 'required|date',               // 日付形式であること
            'start_time' => 'required',              // 開始時間は必須
            'end_time' => 'required|after:start_time', // 終了時間は開始時間より後であること
        ]);

        // バリデーションOKなら確定シフトを新規作成
        FixedShift::create($request->only('user_id', 'date', 'start_time', 'end_time'));

        // 作成後、作成フォームへ戻り成功メッセージを表示
        return redirect()->route('fixed.create')->with('message', '確定シフトを登録しました');
    }

    /**
     * ログインユーザー（一般ユーザー）用：自分の確定シフト一覧を取得し表示
     */
    public function userView()
    {
        // ログイン中ユーザーの確定シフトを取得（日付順）
        $shifts = FixedShift::where('user_id', Auth::id())->orderBy('date')->get();

        // ユーザー用ビューにシフトデータを渡す
        return view('user.fixed_view', compact('shifts'));
    }

    /**
     * 管理者用：特定年月の確定シフトをカレンダー形式で一覧表示
     * GETパラメータ 'ym' で年月指定（例：2025-07）、指定なければ今月を表示
     */
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        // パラメータから年月を取得。未指定なら現在年月
        $yearMonth = $request->input('ym', Carbon::now()->format('Y-m'));
        // 年と月に分解
        [$year, $month] = explode('-', $yearMonth);

        // その年月の1日と末日をCarbonで作成
        $startDate = Carbon::create($year, $month, 1);
        $endDate = (clone $startDate)->endOfMonth();

        // 会社所属ユーザーの中で対象期間の確定シフトを取得し、日付順に並べる
        $shifts = FixedShift::with('user')
            ->whereHas('user', fn($q) => $q->where('company_id', $companyId))
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date')
            ->get();

        // その月の日数を取得
        $daysInMonth = $startDate->daysInMonth;

        // 日付ごとにシフトをグループ化（連想配列）
        $shiftsByDate = [];
        foreach ($shifts as $shift) {
            $shiftsByDate[$shift->date][] = $shift;
        }

        // カレンダー形式表示用のビューに必要な変数を渡す
        return view('admin.fixed_shifts_calendar', compact('yearMonth', 'startDate', 'daysInMonth', 'shiftsByDate'));
    }

    /**
     * 確定シフトの編集フォームを表示
     * @param int $id 確定シフトID
     */
    

    /**
     * 確定シフトの更新処理
     * @param Request $request フォームからの送信データ
     * @param int $id 更新対象の確定シフトID
     */
    public function update(Request $request, $id)
    {
        // 更新対象のシフトを取得
        $shift = FixedShift::findOrFail($id);

        // バリデーション：開始時間必須、終了時間は開始時間より後であること
        $request->validate([
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        // DBを更新
        $shift->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        // 更新後、管理者の確定シフト一覧に戻る
        return redirect()->route('admin.fixed.index')->with('message', '確定シフトを更新しました');
        }

    public function edit($id)
    {
    $shift = FixedShift::with('user')->findOrFail($id);
    return view('admin.fixed_edit', compact('shift'));
    }

    /**
     * 確定シフトを削除する
     * @param int $id 削除対象の確定シフトID
     */
    public function destroy($id)
    {
        // 削除対象シフトを取得
        $shift = FixedShift::findOrFail($id);

        // DBから削除
        $shift->delete();

        // 削除後、一覧に戻りメッセージを表示
        return redirect()->route('admin.fixed.index')->with('message', '確定シフトを削除しました');
    }
}
