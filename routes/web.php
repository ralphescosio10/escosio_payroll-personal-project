<?php

use App\Http\Controllers\AlphalistController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompensationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TaxFormController;
use App\Http\Controllers\TaxSelectionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::post('/employees/import', [EmployeeController::class, 'importCsv'])->name('employees.import');
    Route::get('/employees/export', [EmployeeController::class, 'exportCsv'])->name('employees.export');

    Route::get('/compensation', [CompensationController::class, 'index'])->name('compensation.index');
    Route::get('/compensation/create', [CompensationController::class, 'create'])->name('compensation.create');
    Route::post('/compensation', [CompensationController::class, 'store'])->name('compensation.store');
    Route::post('/compensation-records', [AlphalistController::class, 'storeCompensation'])->name('compensation-records.store');
    Route::get('/compensation/{record}', [CompensationController::class, 'show'])->name('compensation.show');
    Route::get('/compensation/{record}/edit', [CompensationController::class, 'edit'])->name('compensation.edit');
    Route::put('/compensation/{record}', [CompensationController::class, 'update'])->name('compensation.update');
    Route::delete('/compensation/{record}', [CompensationController::class, 'destroy'])->name('compensation.destroy');
    Route::get('/compensation-records/export', [AlphalistController::class, 'exportCompensationCsv'])->name('compensation.export');

    Route::get('/alphalist', [AlphalistController::class, 'alphalistIndex'])->name('alphalist.index');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');

    Route::get('/forms', [TaxFormController::class, 'index'])->name('forms.index');
    Route::get('/forms/choose', [TaxSelectionController::class, 'choose'])->name('forms.choose');
    Route::post('/forms/choose', [TaxSelectionController::class, 'store'])->name('forms.choose.store');
    Route::get('/forms/map', [TaxFormController::class, 'map'])->name('forms.map');
    Route::get('/forms/qap', [TaxFormController::class, 'qap'])->name('forms.qap');
    Route::get('/forms/1604-e', [TaxFormController::class, 'form1604e'])->name('forms.1604e');
    Route::get('/forms/sawt', [TaxFormController::class, 'sawt'])->name('forms.sawt');
    Route::get('/forms/annual', [TaxFormController::class, 'annual'])->name('forms.annual');
});
