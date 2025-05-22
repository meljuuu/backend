<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AuthController;
// use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResearchController;
use App\Http\Controllers\AdminStudentClassController;
use Illuminate\Http\Exceptions\NotFoundHttpException;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherSubjectController;
use App\Http\Controllers\StudentClassController;
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
Route::post('/login', [AuthController::class, 'login'])->name('login');
// Route::get('/profile', [ProfileController::class, 'getProfile']);

// Public route (no auth)
Route::put('/teacher/lesson-plans/{id}', [\App\Http\Controllers\LessonPlanController::class, 'update']);

//STUDENT API
Route::post('/student/add', [StudentController::class, 'store']);
Route::get('/student/getAll', [StudentController::class, 'getAll']);
Route::get('/student/getAllPending', [StudentController::class, 'getPendingStudents']);
Route::get('/student/getAllAccepted', [StudentController::class, 'getAcceptedStudents']);
Route::put('/student/accept/{id}', [StudentController::class, 'acceptProfile']);
Route::post('/student/bulk-upload', [StudentController::class, 'bulkUpload']);


//ADMIN API
Route::post('/assign-students', [AdminStudentClassController::class, 'assignStudentsToClass']);
Route::post('/get-all-classes', [AdminStudentClassController::class, 'indexClass']);
Route::get('/get-super-classes', [AdminStudentClassController::class, 'indexExcludeIncomplete']);
Route::get('/dashboard/students/count', [AdminDashboardController::class, 'getStudentCount']);
Route::get('/dashboard/teachers/count', [AdminDashboardController::class, 'getTeacherCount']);
Route::get('/dashboard/students/gender-distribution', [AdminDashboardController::class, 'getStudentGenderDistribution']);
Route::get('/dashboard/students/grade-distribution', [AdminDashboardController::class, 'getStudentGradeDistribution']);


Route::get('/get-accepted-classes', [AdminStudentClassController::class, 'indexAllAccepted']);
//Teacher
    Route::get('/teacher/getAll', [TeacherController::class, 'getAll']);







//SUPER ADMIN API
Route::get('/superadmin/classes-with-students', [SuperAdminController::class, 'getAllWithStudentCount']);
Route::get('/superadmin/students', [SuperadminController::class, 'getAllStudentsData']);
Route::get('/superadmin/student/{id}', [SuperadminController::class, 'getStudentById']);
Route::put('/superadmin/student/{id}/accept', [SuperadminController::class, 'acceptStudent']);
Route::put('/superadmin/student/{id}/decline', [SuperadminController::class, 'declineStudent']);





//SUBJECTS API
Route::get('/subject/getSubjects', [SubjectController::class, 'getAll']);
    
//TEACHER SUBJECTS
Route::get('/teacher-subjects/getAll', [TeacherSubjectController::class, 'getAllSubject']);

//STUDENTCLASSES
Route::post('/admin/create-class', [StudentClassController::class, 'store']);
Route::get('/admin/get-classes', [StudentClassController::class, 'index']);
Route::post('/admin/add-student-to-class',[StudentClassController::class, 'addStudentsToClass']);
Route::post('/admin/remove-student-to-class',[StudentClassController::class, 'removeStudentsFromClass']);

// Protected Routes Here
Route::middleware('auth:sanctum')->group(function () {

    //PERSONNEL API
    Route::post('/teacher/create-teacher', [TeacherController::class, 'createTeacherAccount']);
    Route::put('/teachers/edit/{teacherId}', [TeacherController::class, 'updateTeacherAccount']);
    Route::delete('/teachers/delete/{teacherId}', [TeacherController::class, 'deleteTeacherAccount']);

    
    Route::get('/teachers/getAll', [TeacherController::class, 'getAllPersonnel']);
    // Route::put('/profile', [ProfileController::class, 'updateProfile']);


    // Route::put('/profile', [ProfileController::class, 'updateProfile']);
    // Route::post('/profile/research', [ProfileController::class, 'addResearch']);
    // Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar']);
    Route::get('/teacher/profile', [TeacherController::class, 'getProfile']);
    Route::put('/teacher/profile', [TeacherController::class, 'updateProfile']);
    Route::post('/teacher/avatar', [TeacherController::class, 'updateAvatar']);
    Route::post('/teacher/research', [ResearchController::class, 'store']);
    Route::delete('/teacher/research/{research}', [ResearchController::class, 'destroy']);
    Route::apiResource('/teacher/lesson-plans', \App\Http\Controllers\LessonPlanController::class);

    });



