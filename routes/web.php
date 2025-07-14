<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\FixedShiftController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CalendarShiftController;
use App\Http\Controllers\AdminCalendarController;
use App\Http\Controllers\ShiftEditController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\CompanyJoinRequestController;
use App\Http\Controllers\AdminCompanyApprovalController;

// トップページ（ログイン前のホーム画面など）※不要なら削除OK
Route::get('/', function () {
    return view('home');
});

// 共通のダッシュボードビュー（管理者・ユーザー用が別にあるなら削除OK）
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// プロフィール編集（Laravel Breezeにある機能）※使わないなら削除OK
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ログインユーザー専用ルートグループ
Route::middleware(['auth'])->group(function () {

    // 一般ユーザーのダッシュボード ※必要
    Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');

    // 管理者のダッシュボード ※必要
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});

// 管理者：会社情報登録・更新画面 ※会社単位の管理が必要なら残す
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/company', [AdminController::class, 'showCompanyForm'])->name('admin.company.form');
    Route::post('/admin/company', [AdminController::class, 'updateCompany'])->name('admin.company.update');
});

// ユーザー：会社参加申請フォーム ※承認制にしたいなら必要
//Route::middleware(['auth'])->group(function () {
//    Route::get('/user/company', [UserController::class, 'showCompanyJoinForm'])->name('user.company.form');
//   Route::post('/user/company', [UserController::class, 'joinCompany'])->name('user.company.join');
//});

// 一般ユーザー：1日ずつのシフト提出（旧方式）※カレンダーに統一なら削除OK
//Route::middleware(['auth'])->group(function () {
//   Route::get('/user/shift', [ShiftController::class, 'create'])->name('shift.create');
//    Route::post('/user/shift', [ShiftController::class, 'store'])->name('shift.store');
//});

// 管理者：1日ずつ提出されたシフトの確認（旧方式）※カレンダー統一なら削除OK
//Route::middleware(['auth'])->group(function () {
//    Route::get('/admin/shifts', [AdminController::class, 'showShifts'])->name('admin.shifts');
//});

// 固定シフト（毎週決まった曜日など）を作成・表示 ※使わないなら削除OK
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/fixed-shifts/create', [FixedShiftController::class, 'create'])->name('fixed.create');
    Route::post('/admin/fixed-shifts/store', [FixedShiftController::class, 'store'])->name('fixed.store');

    // 一般ユーザーが固定シフトを確認する画面
    Route::get('/user/fixed-shifts', [FixedShiftController::class, 'userView'])->name('fixed.user.view');
});

// チャット画面・投稿処理 ※チャットを導入しないなら削除OK
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');
});

// 管理者：ユーザー一覧・削除 ※ユーザー管理に必要
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users');
    Route::delete('/admin/users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
});

// 一般ユーザー：カレンダー形式でのシフト提出（新方式）※必要
Route::middleware(['auth'])->group(function () {
    Route::get('/user/calendar-shift', [CalendarShiftController::class, 'create'])->name('calendar.shift.create');
    Route::post('/user/calendar-shift', [CalendarShiftController::class, 'store'])->name('calendar.shift.store');
});

// 管理者：カレンダーシフトを一覧表示 ※必要
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/calendar-shift', [AdminCalendarController::class, 'index'])->name('admin.calendar.shift');
});

// 管理者：カレンダーシフトを確定 ※必要
Route::middleware(['auth'])->group(function () {
    Route::post('/admin/calendar-shift/fix', [AdminCalendarController::class, 'fixShift'])->name('admin.calendar.fix');
});

// 一般ユーザー：提出済みのシフト編集・削除（旧方式）※不要なら削除OK
Route::middleware(['auth'])->group(function () {
    Route::get('/user/shifts/edit', [ShiftEditController::class, 'index'])->name('user.shifts.edit');
    Route::post('/user/shifts/update/{id}', [ShiftEditController::class, 'update'])->name('user.shifts.update');
    Route::delete('/user/shifts/delete/{id}', [ShiftEditController::class, 'destroy'])->name('user.shifts.delete');
});

// 管理者：固定シフトの一覧・編集・削除 ※不要なら削除OK
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/fixed-shifts', [FixedShiftController::class, 'index'])->name('admin.fixed.index');
    Route::post('/admin/fixed-shifts/update/{id}', [FixedShiftController::class, 'update'])->name('admin.fixed.update');
    Route::delete('/admin/fixed-shifts/delete/{id}', [FixedShiftController::class, 'destroy'])->name('admin.fixed.delete');
});

// URLに年・月を指定してカレンダーシフト作成画面を開く（ユーザー）※必要
Route::get('/user/calendar-shift/{year?}/{month?}', [CalendarShiftController::class, 'create'])->name('calendar.shift.create');

// URLに年・月を指定して管理者がカレンダーシフトを閲覧 ※必要
Route::get('/admin/calendar-shift/{year?}/{month?}', [AdminCalendarController::class, 'index'])->name('admin.calendar.shift');

// 年月指定で提出済みシフトの編集画面へ（ユーザー）※必要
Route::get('/user/edit-shifts/{year?}/{month?}', [UserController::class, 'edit'])->name('user.shifts.edit');

// 管理者：固定シフトの編集（再掲）※重複だが残してもOK、削除もOK
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/fixed-shifts', [FixedShiftController::class, 'index'])->name('admin.fixed.index');
    Route::get('/admin/fixed-shifts/edit/{id}', [FixedShiftController::class, 'edit'])->name('admin.fixed.edit');
    Route::post('/admin/fixed-shifts/update/{id}', [FixedShiftController::class, 'update'])->name('admin.fixed.update');
    Route::delete('/admin/fixed-shifts/delete/{id}', [FixedShiftController::class, 'destroy'])->name('admin.fixed.delete');
});

// 管理者：給与管理画面 ※未実装なら削除してもOK
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/salary', [SalaryController::class, 'index'])->name('admin.salary.index');
});

// ユーザー：会社参加申請（新方式）※承認制にしたいなら必要
//Route::middleware('auth')->group(function () {
//    Route::get('/user/company-request', [CompanyJoinRequestController::class, 'create'])->name('user.company.request');
//   Route::post('/user/company-request', [CompanyJoinRequestController::class, 'store'])->name('user.company.request.store');
//});

// 管理者：会社参加リクエスト一覧と承認・拒否処理 ※新方式に必要
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/company-requests', [AdminCompanyApprovalController::class, 'index'])->name('admin.company.requests');
    Route::post('/admin/company-requests/approve/{id}', [AdminCompanyApprovalController::class, 'approve'])->name('admin.company.requests.approve');
    Route::post('/admin/company-requests/reject/{id}', [AdminCompanyApprovalController::class, 'reject'])->name('admin.company.requests.reject');
});

// 管理者：ユーザー検索・招待
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/users/search', [AdminUserController::class, 'searchForm'])->name('admin.users.searchForm');
    Route::post('/admin/users/search', [AdminUserController::class, 'search'])->name('admin.users.search');
    Route::post('/admin/users/invite/{user}', [AdminUserController::class, 'invite'])->name('admin.users.invite');
});




// Laravel Breezeの認証ルート
require __DIR__.'/auth.php';
