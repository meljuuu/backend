<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResearchController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Exceptions\NotFoundHttpException;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public Routes Here
Route::post('/login', [AuthController::class, 'login']);
Route::get('/profile', [ProfileController::class, 'getProfile']);

// Public route (no auth)
Route::put('/teacher/lesson-plans/{id}', [\App\Http\Controllers\LessonPlanController::class, 'update']);

//STUDENT API
Route::post('/student/add', [StudentController::class, 'store']);
Route::get('/student/getAll', [StudentController::class, 'getAll']);
Route::get('/student/getAllPending', [StudentController::class, 'getPendingStudent']);
Route::get('/student/getAllAccepted', [StudentController::class, 'getAcceptedStudents']);

// Protected Routes Here
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/create-teacher', [TeacherController::class, 'createTeacherAccount']);
    Route::put('/profile', [ProfileController::class, 'updateProfile']);
    Route::post('/profile/research', [ProfileController::class, 'addResearch']);
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar']);
    Route::get('/teacher/profile', [TeacherController::class, 'getProfile']);
    Route::put('/teacher/profile', [TeacherController::class, 'updateProfile']);
    Route::post('/teacher/avatar', [TeacherController::class, 'updateAvatar']);
    Route::post('/teacher/research', [ResearchController::class, 'store']);
    Route::delete('/teacher/research/{research}', [ResearchController::class, 'destroy']);
    Route::apiResource('/teacher/lesson-plans', \App\Http\Controllers\LessonPlanController::class);
    Route::get('/teacher/advisory-stats', [DashboardController::class, 'getAdvisoryStats']);
    Route::get('/teacher/subject-classes', [DashboardController::class, 'getSubjectClasses']);
    Route::get('/teacher/grade-summary', [DashboardController::class, 'getGradeSummary']);
    Route::get('/teacher/recent-grades', [DashboardController::class, 'getRecentGrades']);
   
    // Class Routes
    Route::get('/classes', [ClassController::class, 'getClasses']);
    Route::get('/classes/{classId}', [ClassController::class, 'getClassDetails']);
    Route::get('/classes/{classId}/students', [ClassController::class, 'getClassStudents']);
});



