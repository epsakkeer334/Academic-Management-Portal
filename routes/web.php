<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\Admin\Auth\Login;
use App\Http\Livewire\Admin\Courses\CoursesComponent;
use App\Http\Livewire\Admin\Curriculums\CurriculumsComponent;
use App\Http\Livewire\Admin\Dashboard\Dashboard as AdminDashboard;
use App\Http\Livewire\Admin\Institutes\InstitutesComponent;
use App\Http\Livewire\Admin\Permissions\PermissionsComponent;
use App\Http\Livewire\Admin\Roles\RolesComponent;
use App\Http\Livewire\Admin\Users\UsersComponent;
use App\Http\Livewire\Admin\InstituteUsers\InstituteUsersComponent;
use App\Http\Livewire\Admin\Students\StudentsComponent;
use App\Http\Livewire\Admin\Exams\ExamsComponent;
use App\Http\Livewire\Admin\ExamApplications\ExamApplicationsComponent;
use App\Http\Livewire\Admin\Documents\DocumentsComponent;
use App\Http\Livewire\Admin\Mous\MousComponent;
use App\Http\Livewire\Admin\Compliance\ComplianceComponent;
use App\Http\Livewire\Admin\Reports\ReportsComponent;


// front end section


// admin section

Route::get('admin/login', Login::class)->name('admin.login')->middleware('guest');
Route::get('/admin/login', Login::class)->name('admin.login')->middleware('guest');
Route::get('/logout', function () {Auth::logout();
    return redirect()->route('admin.login');
})->name('admin.logout');


Route::prefix('admin')->middleware(['auth', 'role:super-admin'])->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('admin.dashboard');

    Route::get('/users', UsersComponent::class)->name('admin.users');

    Route::get('/roles', RolesComponent::class)->name('admin.roles');
    Route::get('/permissions', PermissionsComponent::class)->name('admin.permissions');

    Route::get('/courses', CoursesComponent::class)->name('admin.courses');
    Route::get('/curriculums', CurriculumsComponent::class)->name('admin.curriculums');
    Route::get('/institutes', InstitutesComponent::class)->name('admin.institutes');
    Route::get('/institutes/{instituteId}/users', InstituteUsersComponent::class)->name('admin.institute.users');

    Route::get('/students', StudentsComponent::class)->name('admin.students');
    Route::get('/exams', ExamsComponent::class)->name('admin.exams');
    Route::get('/exam-applications', ExamApplicationsComponent::class)->name('admin.exam-applications');
    Route::get('/documents', DocumentsComponent::class)->name('admin.documents');
    Route::get('/mous', MousComponent::class)->name('admin.mous');
    Route::get('/compliance', ComplianceComponent::class)->name('admin.compliance');
    Route::get('/reports', ReportsComponent::class)->name('admin.reports');

});

