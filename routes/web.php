<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Models\Announcement;

// Emergency session fix route - use this if you get 419 errors
Route::get('/fix-session', function () {
    session()->invalidate();
    session()->regenerateToken();
    session()->put('_token', csrf_token());
    
    // Determine where to redirect based on referrer or default to admin login
    $redirect = request()->query('redirect', 'admin');
    $routes = [
        'admin' => '/admin/login',
        'teacher' => '/teacher/login',
        'student' => '/student/login',
        'guardian' => '/guardian/login',
    ];
    
    $destination = $routes[$redirect] ?? '/admin/login';
    return redirect($destination)->with('status', 'Session refreshed! You can now login.');
});

// CSRF token refresh route for AJAX requests
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
})->middleware('web');

Route::get('/', function () {
    $announcements = Announcement::active()->latest()->take(3)->get();
    return view('welcome', compact('announcements'));
});

// Unified login page - automatically detects user role
Route::get('/login', [App\Http\Controllers\UnifiedLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\UnifiedLoginController::class, 'login'])->name('login.submit');

Route::group(['prefix' => 'admin'], function () {
    // Login routes - accessible to everyone except already logged-in admins
    Route::get('/login', [App\Http\Controllers\Admin\LoginController::class, 'showLoginForm'])->name('admin.auth.loginForm');
    Route::post('/login', [App\Http\Controllers\Admin\LoginController::class, 'login'])->name('admin.auth.login');

    // Forgot/Reset Password (OTP)
    Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])->name('admin.auth.forgotForm');
    Route::post('forgot-password', [AuthController::class, 'sendOtp'])->name('admin.auth.forgotSend');
    Route::get('reset-password', [AuthController::class, 'showResetPassword'])->name('admin.auth.resetForm');
    Route::post('reset-password', [AuthController::class, 'resetWithOtp'])->name('admin.auth.resetProcess');

    Route::middleware(['auth:admin,co-admin'])->group(function () {
    // Logout (only when authenticated) - use POST to match the sidebar form
    Route::post('/logout', [App\Http\Controllers\Admin\LoginController::class, 'logout'])->name('admin.auth.logout');

        // Dashboard
        Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.index');
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

        // Profile
        Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('admin.profile.show');
        Route::get('/profile/edit', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('admin.profile.edit');
        Route::put('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('admin.profile.update');
        Route::get('/profile/password/edit', [App\Http\Controllers\Admin\ProfileController::class, 'editPassword'])->name('admin.profile.password.edit');
        Route::put('/profile/password', [App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('admin.profile.password.update');

        // Students
        Route::get('/students', [App\Http\Controllers\Admin\StudentController::class, 'index'])->name('admin.students.index');
        Route::get('/students/create', [App\Http\Controllers\Admin\StudentController::class, 'create'])->name('admin.students.create');
        Route::post('/students/store', [App\Http\Controllers\Admin\StudentController::class, 'store'])->name('admin.students.store');
    Route::post('/students/{student}/generate-password', [App\Http\Controllers\Admin\StudentController::class, 'generatePassword'])->name('admin.students.generate-password');
        Route::post('/students/{student}/add-subject', [App\Http\Controllers\Admin\StudentController::class, 'addSubject'])->name('admin.students.add-subject');
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
        Route::get('/subjects/{subject}/teachers', [App\Http\Controllers\Admin\SubjectController::class, 'teachers'])->name('admin.subjects.teachers');
        Route::get('/subjects/{subject}/edit', [App\Http\Controllers\Admin\SubjectController::class, 'edit'])->name('admin.subjects.edit');
        Route::put('/subjects/{subject}', [App\Http\Controllers\Admin\SubjectController::class, 'update'])->name('admin.subjects.update');
        Route::delete('/subjects/{subject}', [App\Http\Controllers\Admin\SubjectController::class, 'destroy'])->name('admin.subjects.destroy');

        // Strand-Subject linking
        Route::get('/strand-subjects/create', [App\Http\Controllers\Admin\StrandSubjectController::class, 'create'])->name('admin.strand-subjects.create');
        Route::post('/strand-subjects', [App\Http\Controllers\Admin\StrandSubjectController::class, 'store'])->name('admin.strand-subjects.store');
        Route::get('/strand-subjects/{strandSubject}/edit', [App\Http\Controllers\Admin\StrandSubjectController::class, 'edit'])->name('admin.strand-subjects.edit');
        Route::put('/strand-subjects/{strandSubject}', [App\Http\Controllers\Admin\StrandSubjectController::class, 'update'])->name('admin.strand-subjects.update');
        Route::delete('/strand-subjects/{strandSubject}', [App\Http\Controllers\Admin\StrandSubjectController::class, 'destroy'])->name('admin.strand-subjects.destroy');

        // Strands
        Route::get('/strands', [App\Http\Controllers\Admin\StrandController::class, 'index'])->name('admin.strands.index');
        Route::get('/strands/create', [App\Http\Controllers\Admin\StrandController::class, 'create'])->name('admin.strands.create');
        Route::post('/strands', [App\Http\Controllers\Admin\StrandController::class, 'store'])->name('admin.strands.store');
        Route::get('/strands/{strand}', [App\Http\Controllers\Admin\StrandController::class, 'show'])->name('admin.strands.show');
        Route::get('/strands/{strand}/edit', [App\Http\Controllers\Admin\StrandController::class, 'edit'])->name('admin.strands.edit');
        Route::put('/strands/{strand}', [App\Http\Controllers\Admin\StrandController::class, 'update'])->name('admin.strands.update');
        Route::delete('/strands/{strand}', [App\Http\Controllers\Admin\StrandController::class, 'destroy'])->name('admin.strands.destroy');

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
        Route::get('/teachers/{teacher}/assignments', [App\Http\Controllers\Admin\TeacherController::class, 'assignments'])->name('admin.teachers.assignments');
        Route::post('/teachers/{teacher}/assignments', [App\Http\Controllers\Admin\TeacherController::class, 'storeAssignment'])->name('admin.teachers.assignments.store');
        Route::delete('/teachers/{teacher}/assignments/{assignment}', [App\Http\Controllers\Admin\TeacherController::class, 'deleteAssignment'])->name('admin.teachers.assignments.delete');
        Route::get('/teachers/{teacher}/years/{academicYear}/assignments/{assignment}/students', [App\Http\Controllers\Admin\TeacherController::class, 'subjectStudents'])->name('admin.teachers.subject-students');
        Route::get('/teachers/{teacher}/years/{academicYear}/assignments/{assignment}/students/export', [App\Http\Controllers\Admin\TeacherController::class, 'exportSubjectStudents'])->name('admin.teachers.subject-students.export');
        Route::get('/teachers/{teacher}/years/{academicYear}/sections/{sectionAssignment}/students', [App\Http\Controllers\Admin\TeacherController::class, 'sectionStudents'])->name('admin.teachers.section-students');
        Route::get('/teachers/{teacher}/years/{academicYear}/sections/{sectionAssignment}/students/export', [App\Http\Controllers\Admin\TeacherController::class, 'exportSectionStudents'])->name('admin.teachers.section-students.export');

        // Co-Admins (only accessible by main admin)
        Route::middleware(['auth:admin'])->group(function () {
            Route::get('/co-admins', [App\Http\Controllers\Admin\CoAdminController::class, 'index'])->name('admin.co-admins.index');
            Route::get('/co-admins/create', [App\Http\Controllers\Admin\CoAdminController::class, 'create'])->name('admin.co-admins.create');
            Route::post('/co-admins', [App\Http\Controllers\Admin\CoAdminController::class, 'store'])->name('admin.co-admins.store');
            Route::get('/co-admins/{coAdmin}', [App\Http\Controllers\Admin\CoAdminController::class, 'show'])->name('admin.co-admins.show');
            Route::get('/co-admins/{coAdmin}/edit', [App\Http\Controllers\Admin\CoAdminController::class, 'edit'])->name('admin.co-admins.edit');
            Route::put('/co-admins/{coAdmin}', [App\Http\Controllers\Admin\CoAdminController::class, 'update'])->name('admin.co-admins.update');
            Route::delete('/co-admins/{coAdmin}', [App\Http\Controllers\Admin\CoAdminController::class, 'destroy'])->name('admin.co-admins.destroy');
        });

        // Archive
        Route::get('/archive', [App\Http\Controllers\Admin\ArchiveController::class, 'index'])->name('admin.archive.index');
        Route::post('/archive/teachers/{teacher}/restore', [App\Http\Controllers\Admin\ArchiveController::class, 'restoreTeacher'])->name('admin.archive.teachers.restore');
        Route::post('/archive/students/{student}/restore', [App\Http\Controllers\Admin\ArchiveController::class, 'restoreStudent'])->name('admin.archive.students.restore');
        Route::delete('/archive/teachers/{teacher}', [App\Http\Controllers\Admin\ArchiveController::class, 'deleteTeacher'])->name('admin.archive.teachers.delete');
        Route::delete('/archive/students/{student}', [App\Http\Controllers\Admin\ArchiveController::class, 'deleteStudent'])->name('admin.archive.students.delete');

        // Guardians
        Route::get('/guardians', [App\Http\Controllers\Admin\GuardianController::class, 'index'])->name('admin.guardians.index');
        Route::get('/guardians/create', [App\Http\Controllers\Admin\GuardianController::class, 'create'])->name('admin.guardians.create');
        Route::post('/guardians', [App\Http\Controllers\Admin\GuardianController::class, 'store'])->name('admin.guardians.store');
        Route::get('/guardians/{guardian}', [App\Http\Controllers\Admin\GuardianController::class, 'show'])->name('admin.guardians.show');
        Route::get('/guardians/{guardian}/edit', [App\Http\Controllers\Admin\GuardianController::class, 'edit'])->name('admin.guardians.edit');
        Route::put('/guardians/{guardian}', [App\Http\Controllers\Admin\GuardianController::class, 'update'])->name('admin.guardians.update');
        Route::delete('/guardians/{guardian}', [App\Http\Controllers\Admin\GuardianController::class, 'destroy'])->name('admin.guardians.destroy');

        // Sections
        Route::get('/sections', [App\Http\Controllers\Admin\SectionController::class, 'index'])->name('admin.sections.index');
        Route::get('/sections/create', [App\Http\Controllers\Admin\SectionController::class, 'create'])->name('admin.sections.create');
        Route::post('/sections', [App\Http\Controllers\Admin\SectionController::class, 'store'])->name('admin.sections.store');
        Route::get('/sections/{section}/edit', [App\Http\Controllers\Admin\SectionController::class, 'edit'])->name('admin.sections.edit');
        Route::put('/sections/{section}', [App\Http\Controllers\Admin\SectionController::class, 'update'])->name('admin.sections.update');
        Route::delete('/sections/{section}', [App\Http\Controllers\Admin\SectionController::class, 'destroy'])->name('admin.sections.destroy');

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

        // Announcements
        Route::get('/announcements', [App\Http\Controllers\Admin\AnnouncementController::class, 'index'])->name('admin.announcements.index');
        Route::get('/announcements/create', [App\Http\Controllers\Admin\AnnouncementController::class, 'create'])->name('admin.announcements.create');
        Route::post('/announcements', [App\Http\Controllers\Admin\AnnouncementController::class, 'store'])->name('admin.announcements.store');
        Route::get('/announcements/{announcement}', [App\Http\Controllers\Admin\AnnouncementController::class, 'show'])->name('admin.announcements.show');
        Route::get('/announcements/{announcement}/edit', [App\Http\Controllers\Admin\AnnouncementController::class, 'edit'])->name('admin.announcements.edit');
        Route::put('/announcements/{announcement}', [App\Http\Controllers\Admin\AnnouncementController::class, 'update'])->name('admin.announcements.update');
        Route::delete('/announcements/{announcement}', [App\Http\Controllers\Admin\AnnouncementController::class, 'destroy'])->name('admin.announcements.destroy');

        // Messages
        Route::get('/messages', [App\Http\Controllers\Admin\MessageController::class, 'inbox'])->name('admin.messages.inbox');
        Route::get('/messages/compose', [App\Http\Controllers\Admin\MessageController::class, 'compose'])->name('admin.messages.compose');
        Route::post('/messages/send', [App\Http\Controllers\Admin\MessageController::class, 'send'])->name('admin.messages.send');
        Route::get('/messages/{recipient}', [App\Http\Controllers\Admin\MessageController::class, 'show'])->name('admin.messages.show');
        Route::get('/messenger', [App\Http\Controllers\Admin\MessageController::class, 'messenger'])->name('admin.messages.messenger');
        Route::get('/messenger/conversation/{user}', [App\Http\Controllers\Admin\MessageController::class, 'conversation'])->name('admin.messages.conversation');
        Route::post('/messenger/send', [App\Http\Controllers\Admin\MessageController::class, 'sendConversation'])->name('admin.messages.sendConversation');
        Route::get('/messages/{message}/download', [App\Http\Controllers\Admin\MessageController::class, 'downloadAttachment'])->name('admin.messages.download');
        Route::delete('/messages/{message}/unsend', [App\Http\Controllers\Admin\MessageController::class, 'unsendMessage'])->name('admin.messages.unsend');
        // Report a message (admins should be able to report messages too)
        Route::post('/messages/{message}/report', [App\Http\Controllers\Admin\MessageController::class, 'reportMessage'])->name('admin.messages.report');
        Route::get('/api/all-users', [App\Http\Controllers\Admin\MessageController::class, 'getAllUsers'])->name('admin.api.all-users');
        Route::get('/api/unread-count', [App\Http\Controllers\Admin\MessageController::class, 'getUnreadCount'])->name('admin.api.unread-count');
        Route::get('/api/unread-counts-by-partner', [App\Http\Controllers\Admin\MessageController::class, 'getUnreadCountsByPartner'])->name('admin.api.unread-counts-by-partner');

        // Message Reports Management
        Route::get('/message-reports', [App\Http\Controllers\Admin\MessageReportController::class, 'index'])->name('admin.message-reports.index');
        Route::get('/message-reports/{report}', [App\Http\Controllers\Admin\MessageReportController::class, 'show'])->name('admin.message-reports.show');
        Route::post('/message-reports/{report}/status', [App\Http\Controllers\Admin\MessageReportController::class, 'updateStatus'])->name('admin.message-reports.update-status');
        Route::delete('/message-reports/{report}/delete-message', [App\Http\Controllers\Admin\MessageReportController::class, 'deleteMessage'])->name('admin.message-reports.delete-message');
        Route::post('/message-reports/{report}/dismiss', [App\Http\Controllers\Admin\MessageReportController::class, 'dismiss'])->name('admin.message-reports.dismiss');

        // Assigning List
        Route::get('/assigning-list', [App\Http\Controllers\Admin\AssigningListController::class, 'index'])->name('admin.assigning-list.index');
        Route::post('/assigning-list/save-assignments', [App\Http\Controllers\Admin\AssigningListController::class, 'saveAssignments'])->name('admin.assigning-list.save-assignments');

        // Section & Advisers Management (separate page)
        Route::get('/section-advisers', [App\Http\Controllers\Admin\SectionAdviserController::class, 'index'])->name('admin.section-advisers.index');
        Route::get('/section-advisers/grade-11', [App\Http\Controllers\Admin\SectionAdviserController::class, 'grade11'])->name('admin.section-advisers.grade11');
        Route::get('/section-advisers/grade-12', [App\Http\Controllers\Admin\SectionAdviserController::class, 'grade12'])->name('admin.section-advisers.grade12');
        Route::post('/section-advisers/save-advisers', [App\Http\Controllers\Admin\SectionAdviserController::class, 'saveAdvisers'])->name('admin.section-advisers.save-advisers');
        Route::post('/section-advisers/get-students', [App\Http\Controllers\Admin\SectionAdviserController::class, 'getStudents'])->name('admin.section-advisers.get-students');
        Route::post('/section-advisers/get-section-students', [App\Http\Controllers\Admin\SectionAdviserController::class, 'getSectionStudents'])->name('admin.section-advisers.get-section-students');
        Route::post('/section-advisers/get-section-counts', [App\Http\Controllers\Admin\SectionAdviserController::class, 'getSectionCounts'])->name('admin.section-advisers.get-section-counts');
        Route::post('/section-advisers/remove-student', [App\Http\Controllers\Admin\SectionAdviserController::class, 'removeStudent'])->name('admin.section-advisers.remove-student');
        Route::post('/section-advisers/get-subjects', [App\Http\Controllers\Admin\SectionAdviserController::class, 'getSubjects'])->name('admin.section-advisers.get-subjects');
        Route::post('/section-advisers/subject-teachers', [App\Http\Controllers\Admin\SectionAdviserController::class, 'subjectTeachers'])->name('admin.section-advisers.subject-teachers');
        Route::post('/section-advisers/save-subject-teacher', [App\Http\Controllers\Admin\SectionAdviserController::class, 'saveSubjectTeacher'])->name('admin.section-advisers.save-subject-teacher');
        Route::post('/section-advisers/disable-pre-enrollment', [App\Http\Controllers\Admin\SectionAdviserController::class, 'disablePreEnrollment'])->name('admin.section-advisers.disable-pre-enrollment');
        Route::post('/section-advisers/end-of-school-year', [App\Http\Controllers\Admin\SectionAdviserController::class, 'endOfSchoolYear'])->name('admin.section-advisers.end-of-school-year');

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
        
        // Pre-Enrollment Submissions
        Route::get('/pre-enrollments', [App\Http\Controllers\Admin\PreEnrollmentController::class, 'index'])->name('admin.pre-enrollments.index');
        Route::get('/pre-enrollments/{preEnrollment}', [App\Http\Controllers\Admin\PreEnrollmentController::class, 'show'])->name('admin.pre-enrollments.show');
        Route::post('/pre-enrollments/{preEnrollment}/approve', [App\Http\Controllers\Admin\PreEnrollmentController::class, 'approve'])->name('admin.pre-enrollments.approve');
        Route::post('/pre-enrollments/{preEnrollment}/reject', [App\Http\Controllers\Admin\PreEnrollmentController::class, 'reject'])->name('admin.pre-enrollments.reject');
        Route::post('/pre-enrollments/{preEnrollment}/process', [App\Http\Controllers\Admin\PreEnrollmentController::class, 'process'])->name('admin.pre-enrollments.process');
        Route::delete('/pre-enrollments/{preEnrollment}', [App\Http\Controllers\Admin\PreEnrollmentController::class, 'destroy'])->name('admin.pre-enrollments.destroy');
        Route::post('/pre-enrollments/bulk-approve', [App\Http\Controllers\Admin\PreEnrollmentController::class, 'bulkApprove'])->name('admin.pre-enrollments.bulk-approve');
        
    // Messaging (simple inbox/compose)
    Route::get('/messages', [App\Http\Controllers\Admin\MessageController::class, 'inbox'])->name('admin.messages.inbox');
    Route::get('/messages/compose', [App\Http\Controllers\Admin\MessageController::class, 'compose'])->name('admin.messages.compose');
    Route::post('/messages/send', [App\Http\Controllers\Admin\MessageController::class, 'send'])->name('admin.messages.send');
    Route::get('/messages/{recipient}', [App\Http\Controllers\Admin\MessageController::class, 'show'])->name('admin.messages.show');
        
        // Messenger-style UI
        Route::get('/messenger', [App\Http\Controllers\Admin\MessageController::class, 'messenger'])->name('admin.messages.messenger');
        Route::get('/messenger/conversation/{user}', [App\Http\Controllers\Admin\MessageController::class, 'conversation'])->name('admin.messages.conversation');
        Route::post('/messenger/send', [App\Http\Controllers\Admin\MessageController::class, 'sendConversation'])->name('admin.messages.sendConversation');
        Route::get('/messages/{message}/download', [App\Http\Controllers\Admin\MessageController::class, 'downloadAttachment'])->name('admin.messages.download');
        Route::delete('/messages/{message}/unsend', [App\Http\Controllers\Admin\MessageController::class, 'unsendMessage'])->name('admin.messages.unsend');
        
        // API endpoint for user selection
        Route::get('/api/all-users', [App\Http\Controllers\Admin\MessageController::class, 'getAllUsers'])->name('admin.api.all-users');

        // SMS Routes - Semaphore SMS Integration
        Route::get('/sms', [App\Http\Controllers\Admin\SmsController::class, 'index'])->name('admin.sms.index');
        Route::post('/sms/send-single', [App\Http\Controllers\Admin\SmsController::class, 'sendSingle'])->name('admin.sms.send.single');
        Route::post('/sms/send-bulk', [App\Http\Controllers\Admin\SmsController::class, 'sendBulk'])->name('admin.sms.send.bulk');
        Route::get('/sms/balance', [App\Http\Controllers\Admin\SmsController::class, 'getBalance'])->name('admin.sms.balance');
        Route::post('/sms/test', [App\Http\Controllers\Admin\SmsController::class, 'sendTest'])->name('admin.sms.test');
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
        
        // Apply teacher status check middleware to all routes except profile viewing
        Route::middleware(\App\Http\Middleware\CheckTeacherStatus::class)->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\Teacher\DashboardController::class, 'index'])->name('teacher.dashboard');
            Route::get('/subjects', [App\Http\Controllers\Teacher\SubjectController::class, 'index'])->name('teacher.subjects.index');
            Route::get('/class-records', [App\Http\Controllers\Teacher\ClassRecordController::class, 'index'])->name('teacher.class-records.index');
            
            // Student Scores Overview
            Route::get('/scores', [App\Http\Controllers\Teacher\ScoresController::class, 'index'])->name('teacher.scores.index');
            Route::post('/scores', [App\Http\Controllers\Teacher\ScoresController::class, 'store'])->name('teacher.scores.store');
            
            // Messaging
            Route::get('/messages', [App\Http\Controllers\Teacher\MessageController::class, 'inbox'])->name('teacher.messages.inbox');
            Route::get('/messages/compose', [App\Http\Controllers\Teacher\MessageController::class, 'compose'])->name('teacher.messages.compose');
            Route::post('/messages/send', [App\Http\Controllers\Teacher\MessageController::class, 'send'])->name('teacher.messages.send');
            Route::get('/messages/{recipient}', [App\Http\Controllers\Teacher\MessageController::class, 'show'])->name('teacher.messages.show');
            Route::get('/messenger', [App\Http\Controllers\Teacher\MessageController::class, 'messenger'])->name('teacher.messages.messenger');
            Route::get('/messenger/conversation/{user}', [App\Http\Controllers\Teacher\MessageController::class, 'conversation'])->name('teacher.messages.conversation');
            Route::post('/messenger/send', [App\Http\Controllers\Teacher\MessageController::class, 'sendConversation'])->name('teacher.messages.sendConversation');
            Route::post('/messenger/typing', [App\Http\Controllers\Teacher\MessageController::class, 'broadcastTyping'])->name('teacher.messages.typing');
            Route::get('/messages/{message}/download', [App\Http\Controllers\Teacher\MessageController::class, 'downloadAttachment'])->name('teacher.messages.download');
            Route::delete('/messages/{message}/unsend', [App\Http\Controllers\Teacher\MessageController::class, 'unsendMessage'])->name('teacher.messages.unsend');
            Route::post('/messages/{message}/report', [App\Http\Controllers\Teacher\MessageController::class, 'reportMessage'])->name('teacher.messages.report');
            Route::get('/api/unread-counts', [App\Http\Controllers\Teacher\MessageController::class, 'getUnreadCounts'])->name('teacher.api.unreadCounts');
            Route::get('/api/unread-count', [App\Http\Controllers\Teacher\MessageController::class, 'getUnreadCount'])->name('teacher.api.unread-count');
            Route::get('/api/unread-counts-by-partner', [App\Http\Controllers\Teacher\MessageController::class, 'getUnreadCountsByPartner'])->name('teacher.api.unread-counts-by-partner');
            
            // API endpoint for user selection
            Route::get('/api/all-users', [App\Http\Controllers\Teacher\MessageController::class, 'getAllUsers'])->name('teacher.api.allUsers');
            
            Route::get('/class-records/{assignment}', [App\Http\Controllers\Teacher\ClassRecordController::class, 'show'])->name('teacher.class-records.show');
            Route::get('/class-records/{assignment}/students/{student}', [App\Http\Controllers\Teacher\ClassRecordController::class, 'studentShow'])->name('teacher.class-records.students.show');
            // Create placeholders for a specific student's additional term (invoked from New Entry modal)
            Route::post('/class-records/{assignment}/students/{student}/add-term', [App\Http\Controllers\Teacher\ClassRecordController::class, 'addStudentTerm'])->name('teacher.class-records.students.add-term');
            Route::get('/class-records/{assignment}/view/{term}', [App\Http\Controllers\Teacher\ClassRecordController::class, 'termShow'])->name('teacher.class-records.term.show');
            Route::post('/class-records/{assignment}/assessments', [App\Http\Controllers\Teacher\ClassRecordController::class, 'storeAssessment'])->name('teacher.class-records.assessments.store');
            // Teacher-scoped update for assessments
            Route::post('/class-records/{assignment}/assessments/{subjectRecord}/update', [App\Http\Controllers\Teacher\ClassRecordController::class, 'updateAssessment'])->name('teacher.class-records.assessments.update');
            // Teacher-scoped delete for assessments
            Route::delete('/class-records/{assignment}/assessments/{subjectRecord}', [App\Http\Controllers\Teacher\ClassRecordController::class, 'destroyAssessment'])->name('teacher.class-records.assessments.destroy');
            Route::post('/class-records/{assignment}/scores', [App\Http\Controllers\Teacher\ClassRecordController::class, 'storeScores'])->name('teacher.class-records.scores.store');
            Route::post('/class-records/{assignment}/final-grades/submit', [App\Http\Controllers\Teacher\ClassRecordController::class, 'submitFinalGrades'])->name('teacher.class-records.final-grades.submit');
            // Publish/unpublish grades for students
            Route::post('/class-records/{assignment}/toggle-publication', [App\Http\Controllers\Teacher\ClassRecordController::class, 'toggleGradesPublication'])->name('teacher.class-records.toggle-publication');
            // End of school year
            Route::post('/class-records/{assignment}/end-of-school-year', [App\Http\Controllers\Teacher\ClassRecordController::class, 'endOfSchoolYear'])->name('teacher.class-records.end-of-school-year');
                
            // Students routes
            Route::get('/students', [App\Http\Controllers\Teacher\StudentController::class, 'index'])->name('teacher.students.index');
            Route::get('/students/{student}/grades', [App\Http\Controllers\Teacher\StudentController::class, 'showGrades'])->name('teacher.students.grades');
            Route::post('/students/assessments', [App\Http\Controllers\Teacher\StudentController::class, 'storeAssessment'])->name('teacher.assessments.store');
            Route::get('/students/sections/all', [App\Http\Controllers\Teacher\StudentController::class, 'allSections'])->name('teacher.students.all-sections');
            Route::get('/students/sections/{sectionAssignment}', [App\Http\Controllers\Teacher\StudentController::class, 'section'])->name('teacher.students.section');
            
            // Profile update routes (require active status)
            Route::put('/profile', [App\Http\Controllers\Teacher\ProfileController::class, 'update'])->name('teacher.profile.update');
            Route::post('/profile/picture', [App\Http\Controllers\Teacher\ProfileController::class, 'updateProfilePicture'])->name('teacher.profile.picture.update');
            Route::delete('/profile/picture', [App\Http\Controllers\Teacher\ProfileController::class, 'deleteProfilePicture'])->name('teacher.profile.picture.delete');
            Route::put('/profile/password', [App\Http\Controllers\Teacher\ProfileController::class, 'updatePassword'])->name('teacher.profile.password.update');
            Route::get('/profile/subjects', [App\Http\Controllers\Teacher\ProfileController::class, 'allSubjects'])->name('teacher.profile.subjects.all');
        });
        
        // Profile viewing routes (accessible even when inactive)
        Route::get('/profile', [App\Http\Controllers\Teacher\ProfileController::class, 'show'])->name('teacher.profile.show');
        Route::get('/profile/edit', [App\Http\Controllers\Teacher\ProfileController::class, 'edit'])->name('teacher.profile.edit');
        Route::get('/profile/password/edit', [App\Http\Controllers\Teacher\ProfileController::class, 'editPassword'])->name('teacher.profile.password.edit');
        // Disabled: Teachers cannot remove their own assignments (managed by admin only)
        // Route::delete('/profile/adviser/{section}', [App\Http\Controllers\Teacher\ProfileController::class, 'removeAdviserAssignment'])->name('teacher.profile.adviser.remove');
        // Route::delete('/profile/teaching/{assignment}', [App\Http\Controllers\Teacher\ProfileController::class, 'removeTeachingAssignment'])->name('teacher.profile.teaching.remove');
    });
});

// Student Portal
Route::group(['prefix' => 'student'], function () {
    // Login routes - accessible to everyone except already logged-in students
    Route::get('/login', [App\Http\Controllers\Student\LoginController::class, 'showLoginForm'])->name('student.auth.loginForm');
    Route::post('/login', [App\Http\Controllers\Student\LoginController::class, 'login'])->name('student.auth.login');

    Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])->name('student.auth.forgotForm');
    Route::post('forgot-password', [AuthController::class, 'sendOtp'])->name('student.auth.forgotSend');
    Route::get('reset-password', [AuthController::class, 'showResetPassword'])->name('student.auth.resetForm');
    Route::post('reset-password', [AuthController::class, 'resetWithOtp'])->name('student.auth.resetProcess');

    Route::middleware('auth:student')->group(function () {
        Route::post('/logout', [App\Http\Controllers\Student\LoginController::class, 'logout'])->name('student.auth.logout');
        Route::get('/', fn() => redirect()->route('student.dashboard'));
        Route::get('/dashboard', fn() => view('student.dashboard'))->name('student.dashboard');
    Route::get('/academic-years', [App\Http\Controllers\Student\AcademicYearController::class, 'index'])->name('student.academic-years.index');
    Route::get('/subjects', [App\Http\Controllers\Student\SubjectController::class, 'index'])->name('student.subjects.index');
        
        // Grades (includes Decision Support System)
        Route::get('/grades', [App\Http\Controllers\Student\GradeController::class, 'index'])->name('student.grades.index');
        
        // Student Scores (detailed assessment scores)
        Route::get('/scores', [App\Http\Controllers\Student\ScoresController::class, 'index'])->name('student.scores.index');
        
        // Performance Analytics
        Route::get('/performance', [App\Http\Controllers\Student\PerformanceController::class, 'index'])->name('student.performance.index');
        
        // Enhancement (Decision Support System)
        Route::get('/enhancement', [App\Http\Controllers\Student\EnhancementController::class, 'index'])->name('student.enhancement.index');
        
        // Pre-Enrollment routes
        Route::get('/pre-enrollment-test', fn() => view('student.test_pre_enrollment'))->name('student.pre-enrollment.test');
        Route::get('/pre-enrollment', [App\Http\Controllers\Student\PreEnrollmentController::class, 'index'])->name('student.pre-enrollment.index');
        Route::post('/pre-enrollment', [App\Http\Controllers\Student\PreEnrollmentController::class, 'store'])->name('student.pre-enrollment.store');
        Route::post('/pre-enrollment/sections', [App\Http\Controllers\Student\PreEnrollmentController::class, 'getSections'])->name('student.pre-enrollment.sections');
        Route::delete('/pre-enrollment/{id}', [App\Http\Controllers\Student\PreEnrollmentController::class, 'cancel'])->name('student.pre-enrollment.cancel');
        
        // Profile routes
        Route::get('/profile', [App\Http\Controllers\Student\ProfileController::class, 'show'])->name('student.profile.show');
        Route::get('/profile/edit', [App\Http\Controllers\Student\ProfileController::class, 'edit'])->name('student.profile.edit');
        Route::put('/profile', [App\Http\Controllers\Student\ProfileController::class, 'update'])->name('student.profile.update');
        Route::post('/profile/picture', [App\Http\Controllers\Student\ProfileController::class, 'updateProfilePicture'])->name('student.profile.picture.update');
        Route::delete('/profile/picture', [App\Http\Controllers\Student\ProfileController::class, 'deleteProfilePicture'])->name('student.profile.picture.delete');
        Route::get('/profile/password/edit', [App\Http\Controllers\Student\ProfileController::class, 'editPassword'])->name('student.profile.password.edit');
        Route::put('/profile/password', [App\Http\Controllers\Student\ProfileController::class, 'updatePassword'])->name('student.profile.password.update');
        
        // Messaging
        Route::get('/messages', [App\Http\Controllers\Student\MessageController::class, 'inbox'])->name('student.messages.inbox');
        Route::get('/messages/compose', [App\Http\Controllers\Student\MessageController::class, 'compose'])->name('student.messages.compose');
        Route::post('/messages/send', [App\Http\Controllers\Student\MessageController::class, 'send'])->name('student.messages.send');
        Route::get('/messages/{recipient}', [App\Http\Controllers\Student\MessageController::class, 'show'])->name('student.messages.show');
        Route::get('/messages/{message}/download', [App\Http\Controllers\Student\MessageController::class, 'downloadAttachment'])->name('student.messages.download');
        Route::delete('/messages/{message}/unsend', [App\Http\Controllers\Student\MessageController::class, 'unsendMessage'])->name('student.messages.unsend');
        Route::get('/messenger', [App\Http\Controllers\Student\MessageController::class, 'messenger'])->name('student.messages.messenger');
        Route::get('/messenger/conversation/{user}', [App\Http\Controllers\Student\MessageController::class, 'conversation'])->name('student.messages.conversation');
        Route::post('/messenger/send', [App\Http\Controllers\Student\MessageController::class, 'sendConversation'])->name('student.messages.sendConversation');
        Route::post('/messenger/typing', [App\Http\Controllers\Student\MessageController::class, 'broadcastTyping'])->name('student.messages.typing');
        
        // API endpoint for user selection
        Route::get('/api/all-users', [App\Http\Controllers\Student\MessageController::class, 'getAllUsers'])->name('student.api.allUsers');
        Route::get('/api/unread-count', [App\Http\Controllers\Student\MessageController::class, 'getUnreadCount'])->name('student.api.unread-count');
        Route::get('/api/unread-counts-by-partner', [App\Http\Controllers\Student\MessageController::class, 'getUnreadCountsByPartner'])->name('student.api.unread-counts-by-partner');
    });
});

// Media routes to serve files from storage when the public/storage symlink is not available
Route::get('/media/profile_pictures/{filename}', [App\Http\Controllers\MediaController::class, 'profilePicture'])->name('media.profile_picture');

// Guardian Portal
Route::group(['prefix' => 'guardian'], function () {
    // Login routes - accessible to everyone except already logged-in guardians
    Route::get('/login', [App\Http\Controllers\Guardian\LoginController::class, 'showLoginForm'])->name('guardian.auth.loginForm');
    Route::post('/login', [App\Http\Controllers\Guardian\LoginController::class, 'login'])->name('guardian.auth.login');

    Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])->name('guardian.auth.forgotForm');
    Route::post('forgot-password', [AuthController::class, 'sendOtp'])->name('guardian.auth.forgotSend');
    Route::get('reset-password', [AuthController::class, 'showResetPassword'])->name('guardian.auth.resetForm');
    Route::post('reset-password', [AuthController::class, 'resetWithOtp'])->name('guardian.auth.resetProcess');

    Route::middleware('auth:guardian')->group(function () {
        Route::post('/logout', [App\Http\Controllers\Guardian\LoginController::class, 'logout'])->name('guardian.auth.logout');
        Route::get('/', fn() => redirect()->route('guardian.dashboard'));
        Route::get('/dashboard', [App\Http\Controllers\Guardian\DashboardController::class, 'index'])->name('guardian.dashboard');
        Route::get('/students', fn() => view('guardian.students.index'))->name('guardian.students.index');
        
        // Grades
        Route::get('/grades', [App\Http\Controllers\Guardian\GradeController::class, 'index'])->name('guardian.grades.index');
        
        // Enhancement (Decision Support System)
        Route::get('/enhancement', [App\Http\Controllers\Guardian\EnhancementController::class, 'index'])->name('guardian.enhancement.index');
        
        // Profile
        Route::get('/profile', [App\Http\Controllers\Guardian\ProfileController::class, 'show'])->name('guardian.profile.show');
        Route::get('/profile/edit', [App\Http\Controllers\Guardian\ProfileController::class, 'edit'])->name('guardian.profile.edit');
        Route::put('/profile', [App\Http\Controllers\Guardian\ProfileController::class, 'update'])->name('guardian.profile.update');
        Route::put('/profile/password', [App\Http\Controllers\Guardian\ProfileController::class, 'updatePassword'])->name('guardian.profile.updatePassword');
        Route::delete('/profile/picture', [App\Http\Controllers\Guardian\ProfileController::class, 'removeProfilePicture'])->name('guardian.profile.removePicture');
        
        // Messaging
        Route::get('/messages', [App\Http\Controllers\Guardian\MessageController::class, 'inbox'])->name('guardian.messages.inbox');
        Route::get('/messages/compose', [App\Http\Controllers\Guardian\MessageController::class, 'compose'])->name('guardian.messages.compose');
        Route::post('/messages/send', [App\Http\Controllers\Guardian\MessageController::class, 'send'])->name('guardian.messages.send');
        Route::get('/messages/{recipient}', [App\Http\Controllers\Guardian\MessageController::class, 'show'])->name('guardian.messages.show');
        Route::get('/messenger', [App\Http\Controllers\Guardian\MessageController::class, 'messenger'])->name('guardian.messages.messenger');
        Route::get('/messenger/conversation/{user}', [App\Http\Controllers\Guardian\MessageController::class, 'conversation'])->name('guardian.messages.conversation');
        Route::post('/messenger/send', [App\Http\Controllers\Guardian\MessageController::class, 'sendConversation'])->name('guardian.messages.sendConversation');
        Route::get('/messenger/users', [App\Http\Controllers\Guardian\MessageController::class, 'getAllUsers'])->name('guardian.messages.getAllUsers');
        Route::get('/messages/{message}/download', [App\Http\Controllers\Guardian\MessageController::class, 'downloadAttachment'])->name('guardian.messages.downloadAttachment');
        Route::delete('/messages/{message}/unsend', [App\Http\Controllers\Guardian\MessageController::class, 'unsendMessage'])->name('guardian.messages.unsend');
        Route::get('/api/unread-count', [App\Http\Controllers\Guardian\MessageController::class, 'getUnreadCount'])->name('guardian.api.unread-count');
        Route::get('/api/unread-counts-by-partner', [App\Http\Controllers\Guardian\MessageController::class, 'getUnreadCountsByPartner'])->name('guardian.api.unread-counts-by-partner');
    });
});
