<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ConsumerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\InternalReviewController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\SubmissionPeriodController;
use App\Http\Controllers\ThematicReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

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
    Route::get('submissions/datatable', [SubmissionController::class, 'datatable'])->name('submissions.datatable');
    Route::get('submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
    Route::get('submissions/{submission}/edit', [SubmissionController::class, 'edit'])->name('submissions.edit');
    Route::put('submissions/{submission}', [SubmissionController::class, 'update'])->name('submissions.update');
    Route::delete('submissions/{submission}', [SubmissionController::class, 'destroy'])->name('submissions.destroy');
    Route::post('submissions/{submission}/submit', [SubmissionController::class, 'submit'])->name('submissions.submit');

    // Internal institutional approval chain (officer -> supervisor -> accounting officer)
    Route::post('submissions/{submission}/internal-forward', [InternalReviewController::class, 'forward'])->name('submissions.internal.forward');
    Route::post('submissions/{submission}/internal-return-officer', [InternalReviewController::class, 'returnToOfficer'])->name('submissions.internal.return-officer');
    Route::post('submissions/{submission}/internal-approve', [InternalReviewController::class, 'approve'])->name('submissions.internal.approve');
    Route::post('submissions/{submission}/internal-return-supervisor', [InternalReviewController::class, 'returnToSupervisor'])->name('submissions.internal.return-supervisor');

    // Ministry review workflow (reviewer -> final approver)
    Route::post('submissions/{submission}/review', [ReviewController::class, 'startReview'])->name('submissions.review');
    Route::post('submissions/{submission}/recommend', [ReviewController::class, 'recommend'])->name('submissions.recommend');
    Route::post('submissions/{submission}/accept', [ReviewController::class, 'accept'])->name('submissions.accept');
    Route::post('submissions/{submission}/return', [ReviewController::class, 'sendBack'])->name('submissions.return');
    Route::post('submissions/{submission}/reject', [ReviewController::class, 'reject'])->name('submissions.reject');
    Route::post('submissions/{submission}/publish', [ReviewController::class, 'publish'])->name('submissions.publish');

    // Data catalogue & institutions
    Route::get('datasets/datatable', [DatasetController::class, 'datatable'])->name('datasets.datatable');
    Route::resource('datasets', DatasetController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update']);
    Route::get('institutions/datatable', [InstitutionController::class, 'datatable'])->name('institutions.datatable');
    Route::resource('institutions', InstitutionController::class)->except(['show']);

    // Submission periods, data consumers & ministry departments
    Route::get('periods/datatable', [SubmissionPeriodController::class, 'datatable'])->name('periods.datatable');
    Route::resource('periods', SubmissionPeriodController::class)->except(['show']);
    Route::get('consumers/datatable', [ConsumerController::class, 'datatable'])->name('consumers.datatable');
    Route::resource('consumers', ConsumerController::class)->except(['show']);
    Route::get('departments/datatable', [DepartmentController::class, 'datatable'])->name('departments.datatable');
    Route::resource('departments', DepartmentController::class)->except(['show']);

    // Reports
    Route::get('reports/thematic', [ThematicReportController::class, 'index'])->name('reports.thematic');
    Route::get('reports/thematic/{key}', [ThematicReportController::class, 'show'])->name('reports.thematic.show');
    Route::get('reports/thematic/{key}/export', [ThematicReportController::class, 'export'])->name('reports.thematic.export');
    Route::get('reports/consolidated', [ReportController::class, 'consolidated'])->name('reports.consolidated');
    Route::get('reports/compliance', [ReportController::class, 'compliance'])->name('reports.compliance');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('reports/consolidated-data', [ReportController::class, 'consolidatedData'])->name('reports.consolidated.data');
    Route::get('reports/consolidated-detail', [ReportController::class, 'consolidatedDetail'])->name('reports.consolidated.detail');
    Route::get('reports/compliance-data', [ReportController::class, 'complianceData'])->name('reports.compliance.data');

    // Administration
    Route::get('users/datatable', [UserController::class, 'datatable'])->name('users.datatable');
    Route::resource('users', UserController::class)->except(['show']);
    Route::get('roles/datatable', [RoleController::class, 'datatable'])->name('roles.datatable');
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit.index');
    Route::get('audit-logs/datatable', [AuditLogController::class, 'datatable'])->name('audit.datatable');
    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

    // Profile & API tokens
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('profile/tokens', [ProfileController::class, 'createToken'])->name('profile.tokens.create');
    Route::delete('profile/tokens/{tokenId}', [ProfileController::class, 'revokeToken'])->name('profile.tokens.revoke');
});
