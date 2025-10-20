<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ViolationCategoryController;
use App\Http\Controllers\SanctionController;
use App\Http\Controllers\ViolationController;
use App\Http\Controllers\GuidanceController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SchoolYearController;
use App\Http\Controllers\DashboardController;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// auth
Route::middleware('auth:sanctum')->group(function () {});

// Logout
Route::post('/logout', [AuthController::class, 'logout']);

// User
Route::get('/users/search', [UserController::class, 'search']);
Route::get('/users/profile', [UserController::class, 'profile']);
Route::apiResource('/users', UserController::class);

// Siswa
Route::get('/students/search', [StudentController::class, 'search']);
Route::get('/students/search-all', [StudentController::class, 'searchAll']);
Route::get('/students/profile/{userId}', [StudentController::class, 'getProfile']);
Route::get('/students/violation/{userId}', [StudentController::class, 'getViolationHistory']);
Route::get('/students/guidance/{userId}', [StudentController::class, 'getGuidanceHistory']);
Route::put('/students/profile/{userId}/update', [StudentController::class, 'updateProfile']);
Route::apiResource('/students', StudentController::class);

// Kategori pelanggaran
Route::get('/violation-categories/all', [ViolationCategoryController::class, 'all']);
Route::apiResource('violation-categories', ViolationCategoryController::class);

// Sanksi
Route::apiResource('sanctions', SanctionController::class);

// Pelanggaran
Route::apiResource('violations', ViolationController::class);

// Pembinaan
Route::get('/guidances/pending', [GuidanceController::class, 'pending']);
Route::get('/guidances/in-process', [GuidanceController::class, 'inProcess']);
Route::get('/guidances/completed', [GuidanceController::class, 'completed']);
Route::post('/guidances/{id}/generate-sp', [GuidanceController::class, 'generateSp']);
Route::post('/guidances/{id}/send-notification', [GuidanceController::class, 'sendSpNotification']);
Route::apiResource('guidances', GuidanceController::class);

// Laporan
Route::get('/reports/rekap-pelanggaran', [ReportController::class, 'rekapPelanggaranSiswa']);
Route::get('/reports/rekap-pelanggaran/pdf', [ReportController::class, 'rekapPelanggaranSiswaPdf']);
Route::get('/reports/detail-pelanggaran-siswa', [ReportController::class, 'detailPelanggaranSiswa']);
Route::get('/reports/detail-pelanggaran-siswa-pdf', [ReportController::class, 'detailPelanggaranSiswaPdf']);
Route::get('reports/pelanggaran-by-waktu', [ReportController::class, 'pelanggaranByWaktu']);
Route::get('reports/pelanggaran-by-waktu-pdf', [ReportController::class, 'pelanggaranByWaktuPdf']);
Route::get('/reports/guidances', [ReportController::class, 'laporanPembinaan']);
Route::get('/reports/guidances-pdf', [ReportController::class, 'laporanPembinaanPdf']);

// Log
Route::get('/logs', [LogController::class, 'index']);

// Tahun ajaran
Route::prefix('school-years')->group(function () {
  Route::get('/', [SchoolYearController::class, 'index']);
  Route::get('/active', [SchoolYearController::class, 'getActive']);
});

// Dashboard
Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
Route::get('/dashboard/top-students', [DashboardController::class, 'topPelanggar']);
Route::get('/dashboard/top-violations', [DashboardController::class, 'topJenisPelanggaran']);
Route::get('/dashboard/chart-pelanggaran', [DashboardController::class, 'chartPelanggaran']);
Route::get('/dashboard/chart-weekly', [DashboardController::class, 'chartPelanggaranWeekly']);
Route::get('/dashboard/daily-chart', [DashboardController::class, 'violationWeeklyChart']);
