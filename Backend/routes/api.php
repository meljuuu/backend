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
use App\Http\Controllers\AdminStudentClassController;
use Illuminate\Http\Exceptions\NotFoundHttpException;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherSubjectController;
use App\Http\Controllers\StudentClassController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\StudentClassTeacherSubjectController;
use App\Http\Controllers\GradingController;
use App\Http\Controllers\AdvisoryController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\GradesController;
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
Route::get('/student/get-students-no-class', [StudentController::class, 'getNoClassStudents']);
Route::put('/student/accept/{id}', [StudentController::class, 'acceptProfile']);
Route::post('/student/bulk-upload', [StudentController::class, 'bulkUpload']);
// Route::get('/student/get-students-no-class', [StudentController::class, 'getStudentsNoClass']);
Route::put('/students/update/{id}', [StudentController::class, 'update']);

//ADMIN API
Route::post('/assign-students', [AdminStudentClassController::class, 'assignStudentsToClass']);
Route::post('/get-all-classes', [AdminStudentClassController::class, 'indexClass']);
Route::get('/get-super-classes', [AdminStudentClassController::class, 'indexExcludeIncomplete']);
Route::get('/get-accepted-classes', [AdminStudentClassController::class, 'indexAllAccepted']);
Route::get('/get-classes', [AdminStudentClassController::class, 'indexAllAClasses']);
Route::get('/get-accepted-classes', [AdminStudentClassController::class, 'indexAllAccepted']);
Route::get('/get-classes', [AdminStudentClassController::class, 'indexAllAClasses']);
Route::get('/dashboard/students/count', [AdminDashboardController::class, 'getStudentCount']);
Route::get('/dashboard/teachers/count', [AdminDashboardController::class, 'getTeacherCount']);
Route::get('/dashboard/students/gender-distribution', [AdminDashboardController::class, 'getStudentGenderDistribution']);
Route::get('/dashboard/students/grade-distribution', [AdminDashboardController::class, 'getStudentGradeDistribution']);
Route::get('/dashboard/students/latest', [AdminDashboardController::class, 'getLatestUpdatedStudents']);
Route::get('/dashboard/students/status-counts', [AdminDashboardController::class, 'getSubmissionStatusCounts']);
Route::get('dashboard/accepted-classes/count', [AdminDashboardController::class, 'countAcceptedClasses']);

//Teacher
Route::get('/teacher/getAll', [TeacherController::class, 'getAll']);


//SUPER ADMIN API
Route::get('/superadmin/classes-with-students', [SuperAdminController::class, 'getAllWithStudentCount']);
Route::get('/superadmin/students', [SuperadminController::class, 'getAllStudentsData']);
Route::get('/superadmin/student/{id}', [SuperadminController::class, 'getStudentById']);
Route::put('/superadmin/student/{id}/accept', [SuperadminController::class, 'acceptStudent']);
Route::put('/superadmin/student/{id}/decline', [SuperadminController::class, 'declineStudent']);
Route::get('/superadmin/lesson-plans', [SuperadminController::class, 'getAllLessonPlans']);
Route::get('/superadmin/lesson-plans/{id}', [SuperadminController::class, 'getLessonPlanById']);
Route::put('/superadmin/lesson-plans/{id}/approve', [SuperadminController::class, 'approveLessonPlan']);
Route::put('/superadmin/lesson-plans/{id}/decline', [SuperadminController::class, 'rejectLessonPlan']);
Route::get('/superadmin/grading', [SuperadminController::class, 'getAcceptedClassesWithSubjectsTeachersAndStudents']);
Route::get('/super-admin/summary-stats', [SuperAdminController::class, 'getSummaryStats']);
Route::get('/super-admin/recent-faculties', [SuperAdminController::class, 'getRecentFaculties']);



Route::put('/teachers/edit/{teacherId}', [TeacherController::class, 'updateTeacherAccount']);
Route::delete('/teachers/delete/{teacherId}', [TeacherController::class, 'deleteTeacherAccount']);
Route::post('/teacher/create-teacher', [TeacherController::class, 'createTeacherAccount']);



//SUBJECTS API
Route::get('/subject/getSubjects', [SubjectController::class, 'getAll']);
    
//TEACHER SUBJECTS
Route::get('/teacher-subjects/getAll', [TeacherSubjectController::class, 'getAllSubject']);

Route::get('/teacher-subjects/getAll', [TeacherSubjectController::class, 'getAllSubject']);

//STUDENTCLASSES
Route::post('/admin/create-class', [StudentClassController::class, 'store']);
Route::get('/admin/get-classes', [StudentClassController::class, 'index']);
Route::post('/admin/add-student-to-class',[StudentClassController::class, 'addStudentsToClass']);
Route::post('/admin/remove-student-to-class',[StudentClassController::class, 'removeStudentsFromClass']);
Route::delete('/admin/remove-class',[StudentClassController::class, 'destroy']);
Route::post('/admin/accept-class',[StudentClassController::class, 'accept']);
Route::post('/admin/decline-class',[StudentClassController::class, 'reject']);

// Protected Routes Here
Route::middleware('auth:sanctum')->group(function () {



    
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
    Route::get('/teacher/advisory-stats', [DashboardController::class, 'getAdvisoryStats']);
    Route::get('/teacher/subject-classes', [DashboardController::class, 'getSubjectClasses']);
    Route::get('/teacher/grade-summary', [DashboardController::class, 'getGradeSummary']);
    Route::get('/teacher/recent-grades', [DashboardController::class, 'getRecentGrades']);
   
    // Class Routes
    Route::get('/classes', [ClassController::class, 'getClasses']);
    Route::get('/classes/{classId}', [ClassController::class, 'getClassDetails']);
    Route::get('/classes/{classId}/students', [ClassController::class, 'getStudentsForClass']);

    Route::post('/grades/bulk', [GradingController::class, 'submitGrades']);



    Route::get('/grades/subject/{subjectId}/class/{classId}', [GradesController::class, 'getGradesBySubjectAndClass']);
    Route::get('/grades/student/{studentId}/subject/{subjectId}', [GradingController::class, 'getStudentGrades']);
    Route::post('/grades/bulk', [GradingController::class, 'submitGrades']);

    // Grading routes
    Route::prefix('grades')->group(function () {
        Route::get('/subject/{subjectId}', [GradingController::class, 'getSubjectGrades']);
        Route::get('/student/{studentId}/subject/{subjectId}', [GradingController::class, 'getStudentGrades']);
        Route::post('/bulk', [GradingController::class, 'submitGrades']);
        Route::put('/{gradeId}', [GradingController::class, 'updateGrade']);
    });

    Route::get('/teacher/advisory-students', [AdvisoryController::class, 'getAdvisoryStudents']);
    Route::get('/student/{studentId}/subjects', [AdvisoryController::class, 'getStudentSubjects']);
    Route::get('/student/{studentId}/subject/{subjectId}/grades', [AdvisoryController::class, 'getStudentGrades']);
    Route::get('/teacher/student/{studentId}/grades', [AdvisoryController::class, 'getStudentGrades']);

    // Dashboard Routes
    Route::prefix('teacher')->group(function () {
        Route::get('/advisory-stats', [DashboardController::class, 'getAdvisoryStats']);
        Route::get('/subject-classes', [DashboardController::class, 'getSubjectClasses']);
        Route::get('/grade-summary', [DashboardController::class, 'getGradeSummary']);
        Route::get('/recent-grades', [DashboardController::class, 'getRecentGrades']);
    });

    });

// Classes routes
Route::prefix('classes')->group(function () {
    Route::get('/', [ClassesController::class, 'index']);
    Route::get('/{classId}/subjects', [ClassesController::class, 'showSubjectsForClass']);
    Route::get('/{classId}/teachers', [ClassesController::class, 'getTeachersForClass']);
    Route::get('/{subjectId}/students', [ClassesController::class, 'getStudentsForSubject']);
});

// Student Class Teacher Subject routes
Route::prefix('student-class-teacher-subject')->group(function () {
    Route::get('/', [StudentClassTeacherSubjectController::class, 'index']);
    Route::get('/class/{classId}/subjects', [StudentClassTeacherSubjectController::class, 'getSubjectsForClass']);
    Route::get('/class/{classId}/subjects-with-teachers', [StudentClassTeacherSubjectController::class, 'getClassSubjectsWithTeachers']);
    Route::get('/class/{classId}/teachers', [StudentClassTeacherSubjectController::class, 'getTeachersForClass']);
    Route::post('/assign', [StudentClassTeacherSubjectController::class, 'assignSubjectsToClass']);
    Route::post('/remove', [StudentClassTeacherSubjectController::class, 'removeSubjectsFromClass']);
    Route::get('/teacher/{teacherId}/classes', [StudentClassTeacherSubjectController::class, 'getClassesForTeacher']);
    Route::get('/teacher/{teacherId}/class/{classId}/subjects', [StudentClassTeacherSubjectController::class, 'getTeacherSubjectsInClass']);
});

// Teacher Subject routes
Route::prefix('teacher-subjects')->group(function () {
    Route::get('/', [TeacherSubjectController::class, 'getAllSubject']);
    Route::get('/teacher/{teacherId}', [TeacherSubjectController::class, 'getSubjectsByTeacher']);
    Route::get('/subject/{subjectId}', [TeacherSubjectController::class, 'getTeachersBySubject']);
});

// Subject routes
Route::prefix('subjects')->group(function () {
    Route::get('/', [SubjectController::class, 'getAll']);
    Route::get('/{subjectId}/teachers', [SubjectController::class, 'getTeachers']);
    Route::get('/{subjectId}/classes', [SubjectController::class, 'getClasses']);
    Route::post('/grades', [SubjectController::class, 'submitGrades']);
    Route::get('/{subjectId}/grades', [SubjectController::class, 'getGrades']);
});

// Student Class routes
Route::prefix('student-class')->group(function () {
    Route::get('/', [StudentClassController::class, 'index']);
    Route::post('/', [StudentClassController::class, 'store']);
    Route::post('/add-students', [StudentClassController::class, 'addStudentsToClass']);
    Route::post('/remove-students', [StudentClassController::class, 'removeStudentsFromClass']);
    Route::get('/{id}/subjects', [StudentClassController::class, 'show']);
    Route::get('/class/{classId}', [StudentClassController::class, 'getStudentsByClass']);
    Route::get('/class/{classId}', [StudentClassController::class, 'getStudentsByClass']);
});

Route::get('/classes/{classId}', [ClassController::class, 'getClassDetails']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/teacher/advisory-students', [AdvisoryController::class, 'getAdvisoryStudents']);
    Route::get('/student/{studentId}/subjects', [AdvisoryController::class, 'getStudentSubjects']);
});

Route::get('/student-class/class/{classId}', [StudentClassController::class, 'getStudentsByClass']);

//SASettings

Route::post('test/', [SuperadminController::class, 'create']);
Route::get('/testing/subjects', [SuperadminController::class, 'sample']);  
Route::get('testing/{id}', [SuperadminController::class, 'show']); 
Route::put('test/{id}', [SuperadminController::class, 'update']);  
Route::delete('testing/{id}', [SuperadminController::class, 'destroy']); 
Route::get('test/sections', [SuperadminController::class, 'getAllSections']);
Route::get('testings/school-years', [SuperadminController::class, 'getAllSchoolYears']);
Route::post('test/school-years', [SuperadminController::class, 'CreateSchoolYear']);
Route::post('/settings/test/sub', [SuperadminController::class, 'TestSection']);

