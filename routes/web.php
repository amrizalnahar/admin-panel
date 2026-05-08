<?php

use App\Livewire\Admin\BeritaForm;
use App\Livewire\Admin\BeritaTable;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\UserProfile;
use App\Livewire\Admin\KategoriManager;
use App\Livewire\Admin\TagManager;
use App\Livewire\Admin\UserTable;
use App\Livewire\Admin\UserForm;
use App\Livewire\Admin\RoleManager;
use App\Livewire\Admin\SiteSettingsForm;
use App\Livewire\Admin\AuditLogTable;
use App\Livewire\Admin\SystemLogViewer;
use App\Livewire\Admin\EmailTester;
use App\Livewire\Admin\QueueMonitor;
use App\Livewire\Admin\ScheduleTaskManager;
use App\Http\Controllers\Auth\PublicKeyController;
use App\Http\Controllers\Public\BeritaController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/berita', [BeritaController::class, 'index'])->name('berita.index');
Route::get('/berita/{slug}', [BeritaController::class, 'show'])->name('berita.show');

Route::get('/auth/public-key', PublicKeyController::class)
    ->middleware('throttle:10,1')
    ->name('auth.public-key');
Route::get('/robots.txt', function () {
    $robots = file_get_contents(resource_path('views/robots.txt'));

    // Replace APP_URL placeholder
    $robots = str_replace('{{APP_URL}}', config('app.url'), $robots);

    // Replace CRAWL_DELAY placeholder
    $robots = str_replace('{{CRAWL_DELAY}}', config('seo.robots.crawl_delay', 10), $robots);

    // Build Disallow lines from config
    $disallowLines = '';
    foreach (config('seo.robots.disallow', ['/admin/', '/login/']) as $path) {
        $disallowLines .= 'Disallow: ' . trim($path) . "\n";
    }
    $robots = str_replace('{{DISALLOW_PATHS}}', $disallowLines, $robots);

    return response($robots)
        ->header('Content-Type', 'text/plain');
})->name('robots.txt');

Route::get('/admin', function () {
    return auth()->check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('login');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Admin Routes
Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', Dashboard::class)
            ->middleware('permission:dashboard-access')
            ->name('dashboard');

        Route::get('/profil-pengguna', UserProfile::class)
            ->name('profil-pengguna');

        Route::get('/kategori', KategoriManager::class)
            ->middleware('permission:categories-list')
            ->name('kategori');
        Route::get('/tags', TagManager::class)
            ->middleware('permission:tags-list')
            ->name('tags');

        Route::get('/berita', BeritaTable::class)
            ->middleware('permission:posts-list')
            ->name('berita');
        Route::get('/berita/create', BeritaForm::class)
            ->middleware('permission:posts-create')
            ->name('berita.create');
        Route::get('/berita/{post}/edit', BeritaForm::class)
            ->middleware('permission:posts-edit')
            ->name('berita.edit');

    });

// Super Admin Only Routes
Route::middleware(['auth', 'role:super-admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/users', UserTable::class)
            ->middleware('permission:users-list')
            ->name('users');
        Route::get('/users/create', UserForm::class)
            ->middleware('permission:users-create')
            ->name('users.create');
        Route::get('/users/{user}/edit', UserForm::class)
            ->middleware('permission:users-edit')
            ->name('users.edit');

        Route::get('/roles', RoleManager::class)
            ->middleware('permission:roles-list')
            ->name('roles');

        Route::get('/pengaturan', SiteSettingsForm::class)
            ->middleware('permission:settings-list')
            ->name('pengaturan');

        Route::get('/audit-logs', AuditLogTable::class)
            ->middleware('permission:audit-logs-list')
            ->name('audit-logs');

        Route::get('/system-logs', SystemLogViewer::class)
            ->middleware('permission:system-logs-list')
            ->name('system-logs');

        Route::get('/email-tester', EmailTester::class)
            ->middleware('permission:system-email-tester')
            ->name('email-tester');

        Route::get('/queue-monitor', QueueMonitor::class)
            ->middleware('permission:system-queue-monitor')
            ->name('queue-monitor');

        Route::get('/schedule-tasks', ScheduleTaskManager::class)
            ->middleware('permission:schedule-tasks-list')
            ->name('schedule-tasks');
    });

require __DIR__.'/auth.php';
