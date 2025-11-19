<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\BarangayController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (require authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('dashboard');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::put('/reports/{id}/status', [ReportController::class, 'updateStatus'])->name('reports.updateStatus');
    Route::get('/reports/{id}/details', [ReportController::class, 'getDetails'])->name('reports.details');

    // Barangay management routes
    Route::get('/barangays', [BarangayController::class, 'index'])->name('barangays.index');
    Route::get('/barangays/{barangayId}', [BarangayController::class, 'show'])->name('barangays.show');

    Route::get('/users', function () {
        $users = \App\Models\User::with('roles')->orderBy('created_at', 'desc')->get();
        return view('users', compact('users'));
    })->name('users');
    
    // User management routes
    Route::post('/users/{id}/flag', [UserController::class, 'flagUser'])->name('users.flag');
    Route::post('/users/{id}/promote', [UserController::class, 'promoteToOfficer'])->name('users.promote');
    Route::post('/users/{id}/change-role', [UserController::class, 'changeRole'])->name('users.changeRole');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages');
    Route::get('/messages/conversation/{userId}', [MessageController::class, 'getConversation'])->name('messages.conversation');
    Route::get('/messages/list', [MessageController::class, 'getConversationsList'])->name('messages.list');
    Route::post('/messages/send', [MessageController::class, 'sendMessage'])->name('messages.send');
    Route::get('/messages/unread-count', [MessageController::class, 'getUnreadCount'])->name('messages.unread');
    Route::post('/messages/typing', [MessageController::class, 'updateTypingStatus'])->name('messages.typing');
    Route::get('/messages/typing-status/{userId}', [MessageController::class, 'checkTypingStatus'])->name('messages.typingStatus');

    Route::get('/verification', function () {
        return view('verification');
    })->name('verification');

    // API routes for verification management (now properly protected by auth middleware)
    Route::get('/api/verifications/all', [VerificationController::class, 'getAllVerifications']);
    Route::get('/api/verifications/pending', [VerificationController::class, 'getPendingVerifications']);
    Route::post('/api/verification/approve', [VerificationController::class, 'approveVerification']);
    Route::post('/api/verification/reject', [VerificationController::class, 'rejectVerification']);

    Route::get('/statistics', function () {
        return view('statistics');
    })->name('statistics');

    Route::get('/view-map', [MapController::class, 'index'])->name('view-map');
    Route::get('/api/reports', [MapController::class, 'getReports'])->name('api.reports');
});