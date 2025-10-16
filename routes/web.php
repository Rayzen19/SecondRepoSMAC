<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// Provide a generic 'login' route name used by the framework's guest redirect
// This redirects to the admin login form by default so middleware that
// expects route('login') will not fail with a RouteNotFoundException.
Route::get('/login', function () {
    return redirect()->route('admin.auth.loginForm');
})->name('login');

Route::group(['prefix' => 'admin'], function () {
    // Public pages only when not authenticated as admin
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [App\Http\Controllers\Admin\LoginController::class, 'showLoginForm'])->name('admin.auth.loginForm');
        Route::post('/login', [App\Http\Controllers\Admin\LoginController::class, 'login'])->name('admin.auth.login');

        // Forgot/Reset Password (OTP)
        Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])->name('admin.auth.forgotForm');
        Route::post('forgot-password', [AuthController::class, 'sendOtp'])->name('admin.auth.forgotSend');
        Route::get('reset-password', [AuthController::class, 'showResetPassword'])->name('admin.auth.resetForm');
        Route::post('reset-password', [AuthController::class, 'resetWithOtp'])->name('admin.auth.resetProcess');
    });

    Route::middleware(['auth:admin'])->group(function () {
    // Logout (only when authenticated) - use POST to match the sidebar form
    Route::post('/logout', [App\Http\Controllers\Admin\LoginController::class, 'logout'])->name('admin.auth.logout');

        // Dashboard
        Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.index');
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

        // Students
        Route::get('/students', [App\Http\Controllers\Admin\StudentController::class, 'index'])->name('admin.students.index');
        Route::get('/students/create', [App\Http\Controllers\Admin\StudentController::class, 'create'])->name('admin.students.create');
        Route::post('/students/store', [App\Http\Controllers\Admin\StudentController::class, 'store'])->name('admin.students.store');
    Route::post('/students/{student}/generate-password', [App\Http\Controllers\Admin\StudentController::class, 'generatePassword'])->name('admin.students.generate-password');
        Route::get('/students/{student}', [App\Http\Controllers\Admin\StudentController::class, 'show'])->name('admin.students.show');
        Route::get('/students/{student}/enrollments/{studentEnrollment}/records/{subjectRecord}/export', [App\Http\Controllers\Admin\StudentController::class, 'exportSubjectResults'])->name('admin.students.export-subject-results');
        Route::get('/students/{student}/edit', [App\Http\Controllers\Admin\StudentController::class, 'edit'])->name('admin.students.edit');
        Route::put('/students/{student}', [App\Http\Controllers\Admin\StudentController::class, 'update'])->name('admin.students.update');
        Route::delete('/students/{student}', [App\Http\Controllers\Admin\StudentController::class, 'destroy'])->name('admin.students.destroy');

        // Subjects
        Route::get('/subjects', [App\Http\Controllers\Admin\SubjectController::class, 'index'])->name('admin.subjects.index');
        Route::get('/subjects/create', [App\Http\Controllers\Admin\SubjectController::class, 'create'])->name('admin.subjects.create');
        Route::post('/subjects', [App\Http\Controllers\Admin\SubjectController::class, 'store'])->name('admin.subjects.store');
        Route::get('/subjects/{subject}', [App\Http\Controllers\Admin\SubjectController::class, 'show'])->name('admin.subjects.show');
        Route::get('/subjects/{subject}/edit', [App\Http\Controllers\Admin\SubjectController::class, 'edit'])->name('admin.subjects.edit');
        Route::put('/subjects/{subject}', [App\Http\Controllers\Admin\SubjectController::class, 'update'])->name('admin.subjects.update');

        // Strand-Subject linking
        Route::get('/strand-subjects/create', [App\Http\Controllers\Admin\StrandSubjectController::class, 'create'])->name('admin.strand-subjects.create');
        Route::post('/strand-subjects', [App\Http\Controllers\Admin\StrandSubjectController::class, 'store'])->name('admin.strand-subjects.store');
        Route::get('/strand-subjects/{strandSubject}/edit', [App\Http\Controllers\Admin\StrandSubjectController::class, 'edit'])->name('admin.strand-subjects.edit');
        Route::put('/strand-subjects/{strandSubject}', [App\Http\Controllers\Admin\StrandSubjectController::class, 'update'])->name('admin.strand-subjects.update');

        // Strands
        Route::get('/strands', [App\Http\Controllers\Admin\StrandController::class, 'index'])->name('admin.strands.index');
        Route::get('/strands/create', [App\Http\Controllers\Admin\StrandController::class, 'create'])->name('admin.strands.create');
        Route::post('/strands', [App\Http\Controllers\Admin\StrandController::class, 'store'])->name('admin.strands.store');
        Route::get('/strands/{strand}', [App\Http\Controllers\Admin\StrandController::class, 'show'])->name('admin.strands.show');
        Route::get('/strands/{strand}/edit', [App\Http\Controllers\Admin\StrandController::class, 'edit'])->name('admin.strands.edit');
        Route::put('/strands/{strand}', [App\Http\Controllers\Admin\StrandController::class, 'update'])->name('admin.strands.update');

        // Academic Years
        Route::get('/academic-years', [App\Http\Controllers\Admin\AcademicYearController::class, 'index'])->name('admin.academic-years.index');
        Route::get('/academic-years/create', [App\Http\Controllers\Admin\AcademicYearController::class, 'create'])->name('admin.academic-years.create');
        Route::post('/academic-years', [App\Http\Controllers\Admin\AcademicYearController::class, 'store'])->name('admin.academic-years.store');
        Route::get('/academic-years/{academicYear}', [App\Http\Controllers\Admin\AcademicYearController::class, 'show'])->name('admin.academic-years.show');
        Route::post('/academic-years/{academicYear}/sync-subject-enrollments', [App\Http\Controllers\Admin\AcademicYearController::class, 'syncSubjectEnrollments'])->name('admin.academic-years.sync-subject-enrollments');
        Route::get('/academic-years/{academicYear}/edit', [App\Http\Controllers\Admin\AcademicYearController::class, 'edit'])->name('admin.academic-years.edit');
        Route::put('/academic-years/{academicYear}', [App\Http\Controllers\Admin\AcademicYearController::class, 'update'])->name('admin.academic-years.update');

        // Academic Year Strand Advisers
        Route::get('/academic-year-strand-advisers/create', [App\Http\Controllers\Admin\AcademicYearStrandAdviserController::class, 'create'])->name('admin.academic-year-strand-advisers.create');
        Route::post('/academic-year-strand-advisers', [App\Http\Controllers\Admin\AcademicYearStrandAdviserController::class, 'store'])->name('admin.academic-year-strand-advisers.store');
        Route::get('/academic-year-strand-advisers/{adviser}', [App\Http\Controllers\Admin\AcademicYearStrandAdviserController::class, 'show'])->name('admin.academic-year-strand-advisers.show');
        Route::get('/academic-year-strand-advisers/{adviser}/edit', [App\Http\Controllers\Admin\AcademicYearStrandAdviserController::class, 'edit'])->name('admin.academic-year-strand-advisers.edit');
        Route::put('/academic-year-strand-advisers/{adviser}', [App\Http\Controllers\Admin\AcademicYearStrandAdviserController::class, 'update'])->name('admin.academic-year-strand-advisers.update');

        // Academic Year Strand Subjects
        Route::get('/academic-year-strand-subjects/create', [App\Http\Controllers\Admin\AcademicYearStrandSubjectController::class, 'create'])->name('admin.academic-year-strand-subjects.create');
        Route::post('/academic-year-strand-subjects', [App\Http\Controllers\Admin\AcademicYearStrandSubjectController::class, 'store'])->name('admin.academic-year-strand-subjects.store');
        Route::get('/academic-year-strand-subjects/{assignment}/edit', [App\Http\Controllers\Admin\AcademicYearStrandSubjectController::class, 'edit'])->name('admin.academic-year-strand-subjects.edit');
        Route::put('/academic-year-strand-subjects/{assignment}', [App\Http\Controllers\Admin\AcademicYearStrandSubjectController::class, 'update'])->name('admin.academic-year-strand-subjects.update');

        // Academic Year Strand Sections
        Route::get('/academic-year-strand-sections/create', [App\Http\Controllers\Admin\AcademicYearStrandSectionController::class, 'create'])->name('admin.academic-year-strand-sections.create');
        Route::post('/academic-year-strand-sections', [App\Http\Controllers\Admin\AcademicYearStrandSectionController::class, 'store'])->name('admin.academic-year-strand-sections.store');
        Route::get('/academic-year-strand-sections/{assignment}', [App\Http\Controllers\Admin\AcademicYearStrandSectionController::class, 'show'])->name('admin.academic-year-strand-sections.show');
        Route::get('/academic-year-strand-sections/{assignment}/edit', [App\Http\Controllers\Admin\AcademicYearStrandSectionController::class, 'edit'])->name('admin.academic-year-strand-sections.edit');
        Route::put('/academic-year-strand-sections/{assignment}', [App\Http\Controllers\Admin\AcademicYearStrandSectionController::class, 'update'])->name('admin.academic-year-strand-sections.update');

        // Teachers
        Route::get('/teachers', [App\Http\Controllers\Admin\TeacherController::class, 'index'])->name('admin.teachers.index');
        Route::get('/teachers/create', [App\Http\Controllers\Admin\TeacherController::class, 'create'])->name('admin.teachers.create');
        Route::post('/teachers', [App\Http\Controllers\Admin\TeacherController::class, 'store'])->name('admin.teachers.store');
        Route::get('/teachers/{teacher}', [App\Http\Controllers\Admin\TeacherController::class, 'show'])->name('admin.teachers.show');
        Route::get('/teachers/{teacher}/edit', [App\Http\Controllers\Admin\TeacherController::class, 'edit'])->name('admin.teachers.edit');
        Route::put('/teachers/{teacher}', [App\Http\Controllers\Admin\TeacherController::class, 'update'])->name('admin.teachers.update');
    Route::delete('/teachers/{teacher}', [App\Http\Controllers\Admin\TeacherController::class, 'destroy'])->name('admin.teachers.destroy');
        Route::get('/teachers/{teacher}/years/{academicYear}/assignments/{assignment}/students', [App\Http\Controllers\Admin\TeacherController::class, 'subjectStudents'])->name('admin.teachers.subject-students');
        Route::get('/teachers/{teacher}/years/{academicYear}/assignments/{assignment}/students/export', [App\Http\Controllers\Admin\TeacherController::class, 'exportSubjectStudents'])->name('admin.teachers.subject-students.export');
        Route::get('/teachers/{teacher}/years/{academicYear}/sections/{sectionAssignment}/students', [App\Http\Controllers\Admin\TeacherController::class, 'sectionStudents'])->name('admin.teachers.section-students');
        Route::get('/teachers/{teacher}/years/{academicYear}/sections/{sectionAssignment}/students/export', [App\Http\Controllers\Admin\TeacherController::class, 'exportSectionStudents'])->name('admin.teachers.section-students.export');

        // Guardians
        Route::get('/guardians', [App\Http\Controllers\Admin\GuardianController::class, 'index'])->name('admin.guardians.index');
        Route::get('/guardians/create', [App\Http\Controllers\Admin\GuardianController::class, 'create'])->name('admin.guardians.create');
        Route::post('/guardians', [App\Http\Controllers\Admin\GuardianController::class, 'store'])->name('admin.guardians.store');
        Route::get('/guardians/{guardian}', [App\Http\Controllers\Admin\GuardianController::class, 'show'])->name('admin.guardians.show');
        Route::get('/guardians/{guardian}/edit', [App\Http\Controllers\Admin\GuardianController::class, 'edit'])->name('admin.guardians.edit');
        Route::put('/guardians/{guardian}', [App\Http\Controllers\Admin\GuardianController::class, 'update'])->name('admin.guardians.update');

        // Sections
        Route::get('/sections', [App\Http\Controllers\Admin\SectionController::class, 'index'])->name('admin.sections.index');
        Route::get('/sections/create', [App\Http\Controllers\Admin\SectionController::class, 'create'])->name('admin.sections.create');
        Route::post('/sections', [App\Http\Controllers\Admin\SectionController::class, 'store'])->name('admin.sections.store');
        Route::get('/sections/{section}/edit', [App\Http\Controllers\Admin\SectionController::class, 'edit'])->name('admin.sections.edit');
        Route::put('/sections/{section}', [App\Http\Controllers\Admin\SectionController::class, 'update'])->name('admin.sections.update');

        // Assessment Types
        Route::get('/assessment-types', [App\Http\Controllers\Admin\AssessmentTypeController::class, 'index'])->name('admin.assessment-types.index');
        Route::get('/assessment-types/create', [App\Http\Controllers\Admin\AssessmentTypeController::class, 'create'])->name('admin.assessment-types.create');
        Route::post('/assessment-types', [App\Http\Controllers\Admin\AssessmentTypeController::class, 'store'])->name('admin.assessment-types.store');
        Route::get('/assessment-types/{assessmentType}', [App\Http\Controllers\Admin\AssessmentTypeController::class, 'show'])->name('admin.assessment-types.show');
        Route::get('/assessment-types/{assessmentType}/edit', [App\Http\Controllers\Admin\AssessmentTypeController::class, 'edit'])->name('admin.assessment-types.edit');
        Route::put('/assessment-types/{assessmentType}', [App\Http\Controllers\Admin\AssessmentTypeController::class, 'update'])->name('admin.assessment-types.update');
        Route::delete('/assessment-types/{assessmentType}', [App\Http\Controllers\Admin\AssessmentTypeController::class, 'destroy'])->name('admin.assessment-types.destroy');

        // Class Records (Subject Records)
        Route::get('/subject-records', [App\Http\Controllers\Admin\SubjectRecordController::class, 'index'])->name('admin.subject-records.index');
        Route::get('/subject-records/create', [App\Http\Controllers\Admin\SubjectRecordController::class, 'create'])->name('admin.subject-records.create');
        Route::post('/subject-records', [App\Http\Controllers\Admin\SubjectRecordController::class, 'store'])->name('admin.subject-records.store');
        Route::get('/subject-records/{subjectRecord}', [App\Http\Controllers\Admin\SubjectRecordController::class, 'show'])->name('admin.subject-records.show');
        Route::get('/subject-records/{subjectRecord}/edit', [App\Http\Controllers\Admin\SubjectRecordController::class, 'edit'])->name('admin.subject-records.edit');
        Route::get('/subject-records/{subjectRecord}/export', [App\Http\Controllers\Admin\SubjectRecordController::class, 'export'])->name('admin.subject-records.export');
        Route::put('/subject-records/{subjectRecord}', [App\Http\Controllers\Admin\SubjectRecordController::class, 'update'])->name('admin.subject-records.update');
        Route::delete('/subject-records/{subjectRecord}', [App\Http\Controllers\Admin\SubjectRecordController::class, 'destroy'])->name('admin.subject-records.destroy');

        // Subject Record Results (per-student entries for a class record)
        Route::get('/subject-record-results', [App\Http\Controllers\Admin\SubjectRecordResultController::class, 'index'])->name('admin.subject-record-results.index');
        Route::get('/subject-record-results/create', [App\Http\Controllers\Admin\SubjectRecordResultController::class, 'create'])->name('admin.subject-record-results.create');
        Route::post('/subject-record-results', [App\Http\Controllers\Admin\SubjectRecordResultController::class, 'store'])->name('admin.subject-record-results.store');
        Route::get('/subject-record-results/{subjectRecordResult}', [App\Http\Controllers\Admin\SubjectRecordResultController::class, 'show'])->name('admin.subject-record-results.show');
        Route::get('/subject-record-results/{subjectRecordResult}/edit', [App\Http\Controllers\Admin\SubjectRecordResultController::class, 'edit'])->name('admin.subject-record-results.edit');
        Route::put('/subject-record-results/{subjectRecordResult}', [App\Http\Controllers\Admin\SubjectRecordResultController::class, 'update'])->name('admin.subject-record-results.update');
        Route::delete('/subject-record-results/{subjectRecordResult}', [App\Http\Controllers\Admin\SubjectRecordResultController::class, 'destroy'])->name('admin.subject-record-results.destroy');

        // Attendance Logs
        Route::get('/attendance', [App\Http\Controllers\Admin\AttendanceLogController::class, 'index'])->name('admin.attendance.index');
        Route::get('/attendance/create', [App\Http\Controllers\Admin\AttendanceLogController::class, 'create'])->name('admin.attendance.create');
        Route::post('/attendance', [App\Http\Controllers\Admin\AttendanceLogController::class, 'store'])->name('admin.attendance.store');
        Route::get('/attendance/{log}', [App\Http\Controllers\Admin\AttendanceLogController::class, 'show'])->name('admin.attendance.show');
        Route::get('/attendance/{log}/edit', [App\Http\Controllers\Admin\AttendanceLogController::class, 'edit'])->name('admin.attendance.edit');
        Route::put('/attendance/{log}', [App\Http\Controllers\Admin\AttendanceLogController::class, 'update'])->name('admin.attendance.update');
        Route::delete('/attendance/{log}', [App\Http\Controllers\Admin\AttendanceLogController::class, 'destroy'])->name('admin.attendance.destroy');
        Route::get('/attendance-export', [App\Http\Controllers\Admin\AttendanceLogController::class, 'export'])->name('admin.attendance.export');

        // Student Enrollments
        Route::get('/student-enrollments', [App\Http\Controllers\Admin\StudentEnrollmentController::class, 'index'])->name('admin.student-enrollments.index');
        Route::get('/student-enrollments/create', [App\Http\Controllers\Admin\StudentEnrollmentController::class, 'create'])->name('admin.student-enrollments.create');
        Route::post('/student-enrollments', [App\Http\Controllers\Admin\StudentEnrollmentController::class, 'store'])->name('admin.student-enrollments.store');
        Route::get('/student-enrollments/{studentEnrollment}', [App\Http\Controllers\Admin\StudentEnrollmentController::class, 'show'])->name('admin.student-enrollments.show');
        Route::get('/student-enrollments/{studentEnrollment}/edit', [App\Http\Controllers\Admin\StudentEnrollmentController::class, 'edit'])->name('admin.student-enrollments.edit');
        Route::put('/student-enrollments/{studentEnrollment}', [App\Http\Controllers\Admin\StudentEnrollmentController::class, 'update'])->name('admin.student-enrollments.update');
        Route::delete('/student-enrollments/{studentEnrollment}', [App\Http\Controllers\Admin\StudentEnrollmentController::class, 'destroy'])->name('admin.student-enrollments.destroy');
        Route::get('/student-enrollments/sections/options', [App\Http\Controllers\Admin\StudentEnrollmentController::class, 'sectionsOptions'])->name('admin.student-enrollments.sections.options');
        Route::get('/student-enrollments/strands/options', [App\Http\Controllers\Admin\StudentEnrollmentController::class, 'strandsOptions'])->name('admin.student-enrollments.strands.options');
        
    // Messaging (simple inbox/compose)
    Route::get('/messages', [App\Http\Controllers\Admin\MessageController::class, 'inbox'])->name('admin.messages.inbox');
    Route::get('/messages/compose', [App\Http\Controllers\Admin\MessageController::class, 'compose'])->name('admin.messages.compose');
    Route::post('/messages/send', [App\Http\Controllers\Admin\MessageController::class, 'send'])->name('admin.messages.send');
    Route::get('/messages/{recipient}', [App\Http\Controllers\Admin\MessageController::class, 'show'])->name('admin.messages.show');
        
        // Messenger-style UI
        Route::get('/messenger', [App\Http\Controllers\Admin\MessageController::class, 'messenger'])->name('admin.messages.messenger');
        Route::get('/messenger/conversation/{user}', [App\Http\Controllers\Admin\MessageController::class, 'conversation'])->name('admin.messages.conversation');
        Route::post('/messenger/send', [App\Http\Controllers\Admin\MessageController::class, 'sendConversation'])->name('admin.messages.sendConversation');
    });
});

// Teacher Portal
Route::group(['prefix' => 'teacher'], function () {
    Route::middleware('guest:teacher')->group(function () {
        // Use the admin login form for teacher as well (single form for both)
        Route::get('/login', [App\Http\Controllers\Admin\LoginController::class, 'showLoginForm'])->name('teacher.auth.loginForm');
        Route::post('/login', [App\Http\Controllers\Admin\LoginController::class, 'login'])->name('teacher.auth.login');

        // Forgot/Reset (OTP) reuse Admin AuthController for now
        Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])->name('teacher.auth.forgotForm');
        Route::post('forgot-password', [AuthController::class, 'sendOtp'])->name('teacher.auth.forgotSend');
        Route::get('reset-password', [AuthController::class, 'showResetPassword'])->name('teacher.auth.resetForm');
        Route::post('reset-password', [AuthController::class, 'resetWithOtp'])->name('teacher.auth.resetProcess');
    });

    Route::middleware('auth:teacher')->group(function () {
        Route::post('/logout', [App\Http\Controllers\Teacher\LoginController::class, 'logout'])->name('teacher.auth.logout');
        Route::get('/', fn() => redirect()->route('teacher.dashboard'));
        Route::get('/dashboard', fn() => view('teacher.dashboard'))->name('teacher.dashboard');
        Route::get('/subjects', [App\Http\Controllers\Teacher\SubjectController::class, 'index'])->name('teacher.subjects.index');
    Route::get('/class-records', [App\Http\Controllers\Teacher\ClassRecordController::class, 'index'])->name('teacher.class-records.index');
        
        // Profile routes
        Route::get('/profile', [App\Http\Controllers\Teacher\ProfileController::class, 'show'])->name('teacher.profile.show');
        Route::get('/profile/edit', [App\Http\Controllers\Teacher\ProfileController::class, 'edit'])->name('teacher.profile.edit');
        Route::put('/profile', [App\Http\Controllers\Teacher\ProfileController::class, 'update'])->name('teacher.profile.update');
        Route::post('/profile/picture', [App\Http\Controllers\Teacher\ProfileController::class, 'updateProfilePicture'])->name('teacher.profile.picture.update');
        Route::delete('/profile/picture', [App\Http\Controllers\Teacher\ProfileController::class, 'deleteProfilePicture'])->name('teacher.profile.picture.delete');
        Route::get('/profile/password/edit', [App\Http\Controllers\Teacher\ProfileController::class, 'editPassword'])->name('teacher.profile.password.edit');
        Route::put('/profile/password', [App\Http\Controllers\Teacher\ProfileController::class, 'updatePassword'])->name('teacher.profile.password.update');
    Route::get('/class-records/{assignment}', [App\Http\Controllers\Teacher\ClassRecordController::class, 'show'])->name('teacher.class-records.show');
    Route::get('/class-records/{assignment}/students/{student}', [App\Http\Controllers\Teacher\ClassRecordController::class, 'studentShow'])->name('teacher.class-records.students.show');
    Route::get('/class-records/{assignment}/view/{term}', [App\Http\Controllers\Teacher\ClassRecordController::class, 'termShow'])->name('teacher.class-records.term.show');
    Route::post('/class-records/{assignment}/assessments', [App\Http\Controllers\Teacher\ClassRecordController::class, 'storeAssessment'])->name('teacher.class-records.assessments.store');
    Route::post('/class-records/{assignment}/scores', [App\Http\Controllers\Teacher\ClassRecordController::class, 'storeScores'])->name('teacher.class-records.scores.store');
    Route::post('/class-records/{assignment}/final-grades/submit', [App\Http\Controllers\Teacher\ClassRecordController::class, 'submitFinalGrades'])->name('teacher.class-records.final-grades.submit');
    });
});

// Student Portal
Route::group(['prefix' => 'student'], function () {
    Route::middleware('guest:student')->group(function () {
        Route::get('/login', [App\Http\Controllers\Student\LoginController::class, 'showLoginForm'])->name('student.auth.loginForm');
        Route::post('/login', [App\Http\Controllers\Student\LoginController::class, 'login'])->name('student.auth.login');

        Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])->name('student.auth.forgotForm');
        Route::post('forgot-password', [AuthController::class, 'sendOtp'])->name('student.auth.forgotSend');
        Route::get('reset-password', [AuthController::class, 'showResetPassword'])->name('student.auth.resetForm');
        Route::post('reset-password', [AuthController::class, 'resetWithOtp'])->name('student.auth.resetProcess');
    });

    Route::middleware('auth:student')->group(function () {
        Route::post('/logout', [App\Http\Controllers\Student\LoginController::class, 'logout'])->name('student.auth.logout');
        Route::get('/', fn() => redirect()->route('student.dashboard'));
        Route::get('/dashboard', fn() => view('student.dashboard'))->name('student.dashboard');
    Route::get('/academic-years', [App\Http\Controllers\Student\AcademicYearController::class, 'index'])->name('student.academic-years.index');
    Route::get('/subjects', [App\Http\Controllers\Student\SubjectController::class, 'index'])->name('student.subjects.index');
        
        // Grades (includes Decision Support System)
        Route::get('/grades', [App\Http\Controllers\Student\GradeController::class, 'index'])->name('student.grades.index');
        
        // Profile routes
        Route::get('/profile', [App\Http\Controllers\Student\ProfileController::class, 'show'])->name('student.profile.show');
        Route::get('/profile/edit', [App\Http\Controllers\Student\ProfileController::class, 'edit'])->name('student.profile.edit');
        Route::put('/profile', [App\Http\Controllers\Student\ProfileController::class, 'update'])->name('student.profile.update');
        Route::post('/profile/picture', [App\Http\Controllers\Student\ProfileController::class, 'updateProfilePicture'])->name('student.profile.picture.update');
        Route::delete('/profile/picture', [App\Http\Controllers\Student\ProfileController::class, 'deleteProfilePicture'])->name('student.profile.picture.delete');
        Route::get('/profile/password/edit', [App\Http\Controllers\Student\ProfileController::class, 'editPassword'])->name('student.profile.password.edit');
        Route::put('/profile/password', [App\Http\Controllers\Student\ProfileController::class, 'updatePassword'])->name('student.profile.password.update');
    });
});

// Guardian Portal
Route::group(['prefix' => 'guardian'], function () {
    Route::middleware('guest:guardian')->group(function () {
        Route::get('/login', [App\Http\Controllers\Guardian\LoginController::class, 'showLoginForm'])->name('guardian.auth.loginForm');
        Route::post('/login', [App\Http\Controllers\Guardian\LoginController::class, 'login'])->name('guardian.auth.login');

        Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])->name('guardian.auth.forgotForm');
        Route::post('forgot-password', [AuthController::class, 'sendOtp'])->name('guardian.auth.forgotSend');
        Route::get('reset-password', [AuthController::class, 'showResetPassword'])->name('guardian.auth.resetForm');
        Route::post('reset-password', [AuthController::class, 'resetWithOtp'])->name('guardian.auth.resetProcess');
    });

    Route::middleware('auth:guardian')->group(function () {
        Route::post('/logout', [App\Http\Controllers\Guardian\LoginController::class, 'logout'])->name('guardian.auth.logout');
        Route::get('/', fn() => redirect()->route('guardian.dashboard'));
        Route::get('/dashboard', fn() => view('guardian.dashboard'))->name('guardian.dashboard');
        Route::get('/students', fn() => view('guardian.students.index'))->name('guardian.students.index');
    });
});
