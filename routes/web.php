<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Submissions (portal + upload channels)
    Route::get('submissions', [SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('submissions/create', [SubmissionController::class, 'create'])->name('submissions.create');
    Route::post('submissions', [SubmissionController::class, 'store'])->name('submissions.store');
    Route::post('submissions/upload', [SubmissionController::class, 'upload'])->name('submissions.upload');
    Route::get('datasets/{dataset}/template', [SubmissionController::class, 'template'])->name('datasets.template');
    Route::get('submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
    Route::get('submissions/{submission}/edit', [SubmissionController::class, 'edit'])->name('submissions.edit');
    Route::put('submissions/{submission}', [SubmissionController::class, 'update'])->name('submissions.update');
    Route::delete('submissions/{submission}', [SubmissionController::class, 'destroy'])->name('submissions.destroy');
    Route::post('submissions/{submission}/submit', [SubmissionController::class, 'submit'])->name('submissions.submit');

    // Ministry review workflow
    Route::post('submissions/{submission}/review', [ReviewController::class, 'startReview'])->name('submissions.review');
    Route::post('submissions/{submission}/accept', [ReviewController::class, 'accept'])->name('submissions.accept');
    Route::post('submissions/{submission}/return', [ReviewController::class, 'sendBack'])->name('submissions.return');
    Route::post('submissions/{submission}/reject', [ReviewController::class, 'reject'])->name('submissions.reject');
    Route::post('submissions/{submission}/publish', [ReviewController::class, 'publish'])->name('submissions.publish');

    // Data catalogue & institutions
    Route::resource('datasets', DatasetController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update']);
    Route::resource('institutions', InstitutionController::class)->except(['show']);

    // Reports
    Route::get('reports/consolidated', [ReportController::class, 'consolidated'])->name('reports.consolidated');
    Route::get('reports/compliance', [ReportController::class, 'compliance'])->name('reports.compliance');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Administration
    Route::resource('users', UserController::class)->except(['show']);
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit.index');

    // Profile & API tokens
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('profile/tokens', [ProfileController::class, 'createToken'])->name('profile.tokens.create');
    Route::delete('profile/tokens/{tokenId}', [ProfileController::class, 'revokeToken'])->name('profile.tokens.revoke');
});
