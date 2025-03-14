<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\ResumeDownloadController;
use App\Http\Controllers\ResumeCreationController;
use App\Http\Controllers\CoverLetterController;
use App\Http\Controllers\ResumeSectionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Basic Resume CRUD
    Route::get('/resumes', [ResumeController::class, 'index']);
    Route::post('/resumes', [ResumeController::class, 'store']);
    Route::get('/resumes/{id}', [ResumeController::class, 'show']);
    Route::put('/resumes/{id}', [ResumeController::class, 'update']);
    Route::delete('/resumes/{id}', [ResumeController::class, 'destroy']);
    
    // Resume download endpoints
    Route::get('/resumes/{id}/download', [ResumeDownloadController::class, 'downloadResume']);
    Route::get('/resumes/{id}/download-cover-letter', [ResumeDownloadController::class, 'downloadCoverLetter']);
    
    // Resume creation endpoints
    Route::post('/resumes/upload', [ResumeCreationController::class, 'uploadResume']);
    Route::post('/resumes/tailor', [ResumeCreationController::class, 'tailorResume']);
    Route::post('/resumes/from-existing', [ResumeCreationController::class, 'createFromExisting']);
    
    // Cover letter endpoints
    Route::post('/resumes/cover-letter', [CoverLetterController::class, 'generateCoverLetter']);
    Route::post('/resumes/parse-cover-letter', [CoverLetterController::class, 'parseCoverLetter']);
    
    // Resume section regeneration
    Route::post('/resumes/{id}/regenerate/{section}', [ResumeSectionController::class, 'regenerateSection']);
    Route::get('/resumes/{id}/regeneration-limits', [ResumeSectionController::class, 'getRegenerationLimits']);

    Route::get('/user', [UserController::class, 'show']);
    Route::put('/user', [UserController::class, 'update']);
    Route::put('/user/notifications', [UserController::class, 'updateNotifications']);
    Route::delete('/user', [UserController::class, 'destroy']);

    Route::post('/upgrade-plan', [UserController::class, 'upgradePlan']);

    Route::post('/feedback', [FeedbackController::class, 'store']);

    Route::post('/onboarding', [OnboardingController::class, 'store']);
    Route::get('/onboarding/check', [OnboardingController::class, 'check']); 

    Route::post('/logout', [AuthController::class, 'logout']);
});

// Public routes
Route::post('/contact', [ContactController::class, 'store']);