<?php

use App\Http\Controllers\Admin\AdminCatalogModerationController;
use App\Http\Controllers\Admin\AdminPicVerificationController;
use App\Http\Controllers\Application\StudentApplicationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PIC\CatalogManagementController;
use App\Http\Controllers\PIC\DivisionManagementController;
use App\Http\Controllers\PIC\PicApplicationReviewController;
use App\Http\Controllers\PIC\PicRegistrationController;
use App\Http\Controllers\PIC\TeamProfileController;
use App\Http\Controllers\PublicCatalogController;
use Illuminate\Support\Facades\Route;

// Redirect halaman utama ke katalog atau login
Route::get('/', function () {
    return redirect()->route('catalog.index');
});

// Autentikasi Pengguna (Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Katalog OPREC Publik (Dapat Dilihat Tanpa Login Sesuai Standar Portal Kampus)
Route::get('/catalog', [PublicCatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{id}', [PublicCatalogController::class, 'show'])->name('catalog.show');

// Rute Terproteksi (Wajib Login @ugm.ac.id)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Registrasi Menjadi PIC Tim KKN (Mahasiswa)
    Route::get('/pic/register', [PicRegistrationController::class, 'create'])->name('pic.register');
    Route::post('/pic/register', [PicRegistrationController::class, 'store'])->name('pic.register.post');

    // Pendaftaran dan Riwayat Lamaran Mahasiswa
    Route::post('/catalog/{catalogId}/apply', [StudentApplicationController::class, 'store'])->name('applications.store');
    Route::get('/my-applications', [StudentApplicationController::class, 'history'])->name('applications.history');
    Route::post('/applications/{id}/cancel', [StudentApplicationController::class, 'cancel'])->name('applications.cancel');
    Route::get('/documents/{documentId}/view', [StudentApplicationController::class, 'viewDocument'])->name('applications.documents.download');

    // Area Khusus PIC Tim KKN
    Route::middleware('role:pic')->prefix('pic')->name('pic.')->group(function () {
        Route::get('/dashboard', [CatalogManagementController::class, 'index'])->name('dashboard');

        // Pengelolaan Profil Tim & Kontak
        Route::get('/team', [TeamProfileController::class, 'edit'])->name('team.edit');
        Route::put('/team', [TeamProfileController::class, 'update'])->name('team.update');
        Route::post('/team/contacts', [TeamProfileController::class, 'storeContactPerson'])->name('team.contacts.store');
        Route::delete('/team/contacts/{id}', [TeamProfileController::class, 'destroyContactPerson'])->name('team.contacts.destroy');

        // Pengelolaan Katalog OPREC
        Route::get('/catalogs', [CatalogManagementController::class, 'index'])->name('catalogs.index');
        Route::get('/catalogs/create', [CatalogManagementController::class, 'create'])->name('catalogs.create');
        Route::post('/catalogs', [CatalogManagementController::class, 'store'])->name('catalogs.store');
        Route::get('/catalogs/{id}', [CatalogManagementController::class, 'show'])->name('catalogs.show');
        Route::get('/catalogs/{id}/edit', [CatalogManagementController::class, 'edit'])->name('catalogs.edit');
        Route::put('/catalogs/{id}', [CatalogManagementController::class, 'update'])->name('catalogs.update');
        Route::post('/catalogs/{id}/submit-approval', [CatalogManagementController::class, 'submitApproval'])->name('catalogs.submit-approval');
        Route::post('/catalogs/{id}/close', [CatalogManagementController::class, 'closeManually'])->name('catalogs.close');

        // Pengelolaan Divisi & Kuota
        Route::post('/catalogs/{id}/divisions', [DivisionManagementController::class, 'store'])->name('divisions.store');
        Route::delete('/catalogs/{catalogId}/divisions/{divisionId}', [DivisionManagementController::class, 'destroy'])->name('divisions.destroy');

        // Review & Seleksi Pelamar oleh PIC
        Route::get('/catalogs/{catalogId}/applications', [PicApplicationReviewController::class, 'index'])->name('catalogs.applications.index');
        Route::post('/applications/{applicationId}/accept', [PicApplicationReviewController::class, 'accept'])->name('applications.accept');
        Route::post('/applications/{applicationId}/reject', [PicApplicationReviewController::class, 'reject'])->name('applications.reject');
    });

    // Area Khusus Administrator
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminPicVerificationController::class, 'index'])->name('dashboard');

        // Verifikasi Registrasi PIC
        Route::get('/pic-registrations', [AdminPicVerificationController::class, 'index'])->name('pic-registrations.index');
        Route::post('/pic-registrations/{id}/approve', [AdminPicVerificationController::class, 'approve'])->name('pic-registrations.approve');
        Route::post('/pic-registrations/{id}/reject', [AdminPicVerificationController::class, 'reject'])->name('pic-registrations.reject');

        // Moderasi Katalog OPREC
        Route::get('/catalogs', [AdminCatalogModerationController::class, 'index'])->name('catalogs.index');
        Route::get('/catalogs/{id}', [AdminCatalogModerationController::class, 'show'])->name('catalogs.show');
        Route::post('/catalogs/{id}/approve', [AdminCatalogModerationController::class, 'approve'])->name('catalogs.approve');
        Route::post('/catalogs/{id}/reject', [AdminCatalogModerationController::class, 'reject'])->name('catalogs.reject');
    });
});
