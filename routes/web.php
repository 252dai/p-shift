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


Route::get('/', function () {
    return view('home');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 認証された人だけがアクセスできるようにミドルウェアを使う
Route::middleware(['auth'])->group(function () {

    // 一般ユーザー用
    Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');

    // 管理者用
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/company', [AdminController::class, 'showCompanyForm'])->name('admin.company.form');
    Route::post('/admin/company', [AdminController::class, 'updateCompany'])->name('admin.company.update');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/user/company', [UserController::class, 'showCompanyJoinForm'])->name('user.company.form');
    Route::post('/user/company', [UserController::class, 'joinCompany'])->name('user.company.join');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/user/shift', [ShiftController::class, 'create'])->name('shift.create');
    Route::post('/user/shift', [ShiftController::class, 'store'])->name('shift.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/shifts', [AdminController::class, 'showShifts'])->name('admin.shifts');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/fixed-shifts/create', [FixedShiftController::class, 'create'])->name('fixed.create');
    Route::post('/admin/fixed-shifts/store', [FixedShiftController::class, 'store'])->name('fixed.store');

    Route::get('/user/fixed-shifts', [FixedShiftController::class, 'userView'])->name('fixed.user.view');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users');
    Route::delete('/admin/users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/user/calendar-shift', [CalendarShiftController::class, 'create'])->name('calendar.shift.create');
    Route::post('/user/calendar-shift', [CalendarShiftController::class, 'store'])->name('calendar.shift.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/calendar-shift', [AdminCalendarController::class, 'index'])->name('admin.calendar.shift');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/admin/calendar-shift/fix', [AdminCalendarController::class, 'fixShift'])->name('admin.calendar.fix');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/user/shifts/edit', [ShiftEditController::class, 'index'])->name('user.shifts.edit');
    Route::post('/user/shifts/update/{id}', [ShiftEditController::class, 'update'])->name('user.shifts.update');
    Route::delete('/user/shifts/delete/{id}', [ShiftEditController::class, 'destroy'])->name('user.shifts.delete');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/fixed-shifts', [FixedShiftController::class, 'index'])->name('admin.fixed.index');
    Route::post('/admin/fixed-shifts/update/{id}', [FixedShiftController::class, 'update'])->name('admin.fixed.update');
    Route::delete('/admin/fixed-shifts/delete/{id}', [FixedShiftController::class, 'destroy'])->name('admin.fixed.delete');
});

Route::get('/user/calendar-shift/{year?}/{month?}', [CalendarShiftController::class, 'create'])
    ->name('calendar.shift.create');

Route::get('/admin/calendar-shift/{year?}/{month?}', [AdminCalendarController::class, 'index'])
    ->name('admin.calendar.shift');

Route::get('/user/edit-shifts/{year?}/{month?}', [UserController::class, 'edit'])
    ->name('user.shifts.edit');

Route::middleware(['auth'])->group(function () {

    Route::get('/admin/fixed-shifts', [FixedShiftController::class, 'index'])->name('admin.fixed.index');

    Route::get('/admin/fixed-shifts/edit/{id}', [FixedShiftController::class, 'edit'])->name('admin.fixed.edit');

    Route::post('/admin/fixed-shifts/update/{id}', [FixedShiftController::class, 'update'])->name('admin.fixed.update');

    Route::delete('/admin/fixed-shifts/delete/{id}', [FixedShiftController::class, 'destroy'])->name('admin.fixed.delete');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/salary', [SalaryController::class, 'index'])->name('admin.salary.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/user/company-request', [CompanyJoinRequestController::class, 'create'])->name('user.company.request');
    Route::post('/user/company-request', [CompanyJoinRequestController::class, 'store'])->name('user.company.request.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/company-requests', [AdminCompanyApprovalController::class, 'index'])->name('admin.company.requests');
    Route::post('/admin/company-requests/approve/{id}', [AdminCompanyApprovalController::class, 'approve'])->name('admin.company.requests.approve');
    Route::post('/admin/company-requests/reject/{id}', [AdminCompanyApprovalController::class, 'reject'])->name('admin.company.requests.reject');
});

require __DIR__.'/auth.php';
