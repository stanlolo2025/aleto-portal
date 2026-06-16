<?php

use App\Http\Controllers\Api\AuditController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FraudController;
use App\Http\Controllers\Api\GrantController;
use App\Http\Controllers\Api\LifecycleController;
use App\Http\Controllers\Api\ProxyController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VillagerController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/mfa/verify', [AuthController::class, 'verifyMfa']);
Route::post('/auth/mfa/resend', [AuthController::class, 'resendMfa']);

// Public enquiry routes
Route::post('/enquiries', [\App\Http\Controllers\Api\EnquiryController::class, 'store']);
Route::post('/enquiries/track', [\App\Http\Controllers\Api\EnquiryController::class, 'track']);
Route::get('/public/announcements', [\App\Http\Controllers\Api\AnnouncementController::class, 'index']);

// Public access module
Route::post('/public/search', [\App\Http\Controllers\Api\PublicController::class, 'search']);
Route::get('/public/stats', [\App\Http\Controllers\Api\PublicController::class, 'stats']);
Route::get('/public/reports', [\App\Http\Controllers\Api\PublicController::class, 'reports']);

// Export routes (auth via token query param for browser download)
Route::get('/grants/{grantId}/beneficiaries/export/{format}', [GrantController::class, 'exportList']);

// Protected routes
Route::middleware(['auth:sanctum', \App\Http\Middleware\SessionTimeout::class])->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::get('/dashboard/stats', [\App\Http\Controllers\Api\DashboardController::class, 'stats']);
    Route::get('/announcements', [\App\Http\Controllers\Api\AnnouncementController::class, 'index']);

    // Admin-only routes
    Route::middleware(\App\Http\Middleware\CheckRole::class . ':admin')->group(function () {

        // Villager management
        Route::get('/villagers', [VillagerController::class, 'index']);
        Route::post('/villagers', [VillagerController::class, 'store']);
        Route::get('/villagers/{uniqueId}', [VillagerController::class, 'show']);
        Route::put('/villagers/{uniqueId}', [VillagerController::class, 'update']);
        Route::post('/villagers/{uniqueId}/status', [LifecycleController::class, 'changeStatus']);
        Route::get('/villagers/{uniqueId}/transitions', [LifecycleController::class, 'getAllowedTransitions']);
        Route::post('/villagers/{uniqueId}/biometric/verify', [VillagerController::class, 'verifyBiometric']);

        // Proxy management
        Route::get('/villagers/{uniqueId}/proxy', [ProxyController::class, 'show']);
        Route::post('/villagers/{uniqueId}/proxy', [ProxyController::class, 'store']);
        Route::delete('/villagers/{uniqueId}/proxy', [ProxyController::class, 'destroy']);

        // Family members
        Route::get('/villagers/{uniqueId}/family', [\App\Http\Controllers\Api\VillagerController::class, 'getFamily']);
        Route::post('/villagers/{uniqueId}/family', [\App\Http\Controllers\Api\VillagerController::class, 'addFamily']);
        Route::delete('/villagers/family/{id}', [\App\Http\Controllers\Api\VillagerController::class, 'removeFamily']);

        // Grant management
        Route::get('/grants', [GrantController::class, 'index']);
        Route::post('/grants', [GrantController::class, 'store']);
        Route::get('/grants/history/all', [GrantController::class, 'getAllGrantHistory']);
        Route::get('/grants/history/export', [GrantController::class, 'exportGrantHistory']);
        Route::get('/grants/{grantId}/eligible', [GrantController::class, 'getEligible']);
        Route::get('/grants/{grantId}/beneficiaries/lists', [GrantController::class, 'getBeneficiaryLists']);
        Route::get('/grants/{grantId}/history', [GrantController::class, 'getGrantHistory']);
        Route::post('/grants/{grantId}/history', [GrantController::class, 'addGrantHistory']);
        Route::post('/grants/{grantId}/beneficiaries', [GrantController::class, 'createBeneficiaryList']);
        Route::post('/grants/{grantId}/beneficiaries/approve', [GrantController::class, 'approveBeneficiaryList']);

        // Fraud management
        Route::get('/fraud/flagged', [FraudController::class, 'listFlagged']);
        Route::post('/fraud/flagged/{id}/resolve', [FraudController::class, 'resolve']);

        // User management
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}/role', [UserController::class, 'changeRole']);

        // Healthcare Module
        Route::get('/healthcare/medical-records', [\App\Http\Controllers\Api\HealthcareController::class, 'getMedicalRecords']);
        Route::post('/healthcare/medical-records', [\App\Http\Controllers\Api\HealthcareController::class, 'storeMedicalRecord']);
        Route::get('/healthcare/clinic-visits', [\App\Http\Controllers\Api\HealthcareController::class, 'getClinicVisits']);
        Route::post('/healthcare/clinic-visits', [\App\Http\Controllers\Api\HealthcareController::class, 'storeClinicVisit']);
        Route::get('/healthcare/alerts', [\App\Http\Controllers\Api\HealthcareController::class, 'getAlerts']);
        Route::post('/healthcare/alerts', [\App\Http\Controllers\Api\HealthcareController::class, 'storeAlert']);
        Route::post('/healthcare/alerts/{id}/complete', [\App\Http\Controllers\Api\HealthcareController::class, 'completeAlert']);
        Route::get('/healthcare/health-grants', [\App\Http\Controllers\Api\HealthcareController::class, 'getHealthGrants']);
        Route::post('/healthcare/health-grants', [\App\Http\Controllers\Api\HealthcareController::class, 'storeHealthGrant']);
        Route::post('/healthcare/health-grants/{id}/approve', [\App\Http\Controllers\Api\HealthcareController::class, 'approveHealthGrant']);
        Route::post('/healthcare/health-grants/{id}/paid', [\App\Http\Controllers\Api\HealthcareController::class, 'markHealthGrantPaid']);

        // Education Module
        Route::get('/education/students', [\App\Http\Controllers\Api\EducationController::class, 'getStudents']);
        Route::post('/education/students', [\App\Http\Controllers\Api\EducationController::class, 'storeStudent']);
        Route::put('/education/students/{id}', [\App\Http\Controllers\Api\EducationController::class, 'updateStudent']);
        Route::get('/education/scholarships', [\App\Http\Controllers\Api\EducationController::class, 'getScholarships']);
        Route::post('/education/scholarships', [\App\Http\Controllers\Api\EducationController::class, 'storeScholarship']);
        Route::post('/education/scholarships/{id}/approve', [\App\Http\Controllers\Api\EducationController::class, 'approveScholarship']);
        Route::post('/education/scholarships/{id}/paid', [\App\Http\Controllers\Api\EducationController::class, 'markScholarshipPaid']);
        Route::get('/education/exams', [\App\Http\Controllers\Api\EducationController::class, 'getExamResults']);
        Route::post('/education/exams', [\App\Http\Controllers\Api\EducationController::class, 'storeExamResult']);
        Route::get('/education/literacy', [\App\Http\Controllers\Api\EducationController::class, 'getLiteracyPrograms']);
        Route::post('/education/literacy', [\App\Http\Controllers\Api\EducationController::class, 'storeLiteracyProgram']);
        Route::put('/education/literacy/{id}', [\App\Http\Controllers\Api\EducationController::class, 'updateLiteracyProgram']);

        // Development Projects Module
        Route::get('/projects', [\App\Http\Controllers\Api\ProjectsController::class, 'index']);
        Route::post('/projects', [\App\Http\Controllers\Api\ProjectsController::class, 'store']);
        Route::get('/projects/{projectId}', [\App\Http\Controllers\Api\ProjectsController::class, 'show']);
        Route::put('/projects/{projectId}', [\App\Http\Controllers\Api\ProjectsController::class, 'update']);
        Route::get('/projects/{projectId}/beneficiaries', [\App\Http\Controllers\Api\ProjectsController::class, 'getBeneficiaries']);
        Route::post('/projects/{projectId}/beneficiaries', [\App\Http\Controllers\Api\ProjectsController::class, 'addBeneficiary']);
        Route::get('/projects/{projectId}/feedback', [\App\Http\Controllers\Api\ProjectsController::class, 'getFeedback']);
        Route::post('/projects/{projectId}/feedback', [\App\Http\Controllers\Api\ProjectsController::class, 'addFeedback']);
        Route::get('/projects/{projectId}/impact', [\App\Http\Controllers\Api\ProjectsController::class, 'getImpact']);
        Route::post('/projects/{projectId}/impact', [\App\Http\Controllers\Api\ProjectsController::class, 'addImpact']);

        // Households
        Route::get('/households', [\App\Http\Controllers\Api\HouseholdController::class, 'index']);
        Route::get('/households/locations', [\App\Http\Controllers\Api\HouseholdController::class, 'villages']);
        Route::get('/households/{householdId}', [\App\Http\Controllers\Api\HouseholdController::class, 'show']);

        // Announcements
        Route::get('/announcements/all', [\App\Http\Controllers\Api\AnnouncementController::class, 'all']);
        Route::post('/announcements', [\App\Http\Controllers\Api\AnnouncementController::class, 'store']);
        Route::post('/announcements/{id}/deactivate', [\App\Http\Controllers\Api\AnnouncementController::class, 'deactivate']);

        // Enquiries (Admin)
        Route::get('/enquiries', [\App\Http\Controllers\Api\EnquiryController::class, 'index']);
        Route::post('/enquiries/{id}/respond', [\App\Http\Controllers\Api\EnquiryController::class, 'respond']);

        // Messages
        Route::get('/messages/inbox', [\App\Http\Controllers\Api\MessageController::class, 'inbox']);
        Route::get('/messages/sent', [\App\Http\Controllers\Api\MessageController::class, 'sent']);
        Route::get('/messages/unread-count', [\App\Http\Controllers\Api\MessageController::class, 'unreadCount']);
        Route::post('/messages/send', [\App\Http\Controllers\Api\MessageController::class, 'send']);
        Route::post('/messages/{id}/read', [\App\Http\Controllers\Api\MessageController::class, 'read']);

        // Reports
        Route::get('/reports', [\App\Http\Controllers\Api\ReportsController::class, 'availableReports']);
        Route::post('/reports/generate', [\App\Http\Controllers\Api\ReportsController::class, 'generate']);

        // Payment Runs
        Route::get('/payment-runs', [\App\Http\Controllers\Api\PaymentRunController::class, 'index']);
        Route::post('/payment-runs', [\App\Http\Controllers\Api\PaymentRunController::class, 'create']);
        Route::get('/payment-runs/{runId}', [\App\Http\Controllers\Api\PaymentRunController::class, 'show']);
        Route::post('/payment-runs/items/{itemId}/paid', [\App\Http\Controllers\Api\PaymentRunController::class, 'markPaid']);
        Route::post('/payment-runs/items/{itemId}/failed', [\App\Http\Controllers\Api\PaymentRunController::class, 'markFailed']);
    });

    // Admin + Auditor routes
    Route::middleware(\App\Http\Middleware\CheckRole::class . ':admin,auditor')->group(function () {
        Route::get('/audit', [AuditController::class, 'index']);
        Route::get('/audit/export', [AuditController::class, 'export']);
    });

    // Admin + Government Official routes
    Route::middleware(\App\Http\Middleware\CheckRole::class . ':admin,government_official')->group(function () {
        Route::get('/audit/report', [AuditController::class, 'report']);
    });
});
