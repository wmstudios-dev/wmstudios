<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\ThoughtController;
use App\Http\Controllers\WorkController;
use App\Support\AdminResources;
use Illuminate\Support\Facades\Route;

// Public site
Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/works', [WorkController::class, 'index'])->name('works.index');
Route::get('/works/{work}', [WorkController::class, 'show'])->name('works.show');
Route::get('/services', [SiteController::class, 'services'])->name('services');
Route::get('/process', [SiteController::class, 'process'])->name('process');
Route::get('/space', [SiteController::class, 'space'])->name('space');
Route::get('/thoughts', [ThoughtController::class, 'index'])->name('thoughts.index');
Route::get('/thoughts/{thought}', [ThoughtController::class, 'show'])->name('thoughts.show');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::get('/privacy', [LegalController::class, 'privacy'])->name('privacy');
Route::get('/terms', [LegalController::class, 'terms'])->name('terms');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1,contact')->name('contact.store');

Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

// Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [Admin\AuthController::class, 'show'])->name('login');
    Route::post('/login', [Admin\AuthController::class, 'login'])->middleware('throttle:6,1,adminlogin')->name('login.post');
    Route::post('/logout', [Admin\AuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::redirect('/', '/admin/dashboard');
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/messages', [Admin\MessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/export', [Admin\MessageController::class, 'export'])->name('messages.export');
        Route::get('/messages/{message}', [Admin\MessageController::class, 'show'])->name('messages.show');
        Route::patch('/messages/{message}', [Admin\MessageController::class, 'update'])->name('messages.update');
        Route::delete('/messages/{message}', [Admin\MessageController::class, 'destroy'])->name('messages.destroy');

        Route::get('/settings', [Admin\SettingsController::class, 'edit'])->name('settings.edit');
        Route::post('/settings', [Admin\SettingsController::class, 'update'])->name('settings.update');

        // Works, services, packages, FAQ, process, space, thoughts, clients, testimonials:
        // one generic controller driven by App\Support\AdminResources.
        $keys = implode('|', AdminResources::keys());

        Route::get('/{resource}', [Admin\ResourceController::class, 'index'])->where('resource', $keys)->name('resource.index');
        Route::get('/{resource}/create', [Admin\ResourceController::class, 'create'])->where('resource', $keys)->name('resource.create');
        Route::post('/{resource}', [Admin\ResourceController::class, 'store'])->where('resource', $keys)->name('resource.store');
        Route::get('/{resource}/{id}/edit', [Admin\ResourceController::class, 'edit'])->where('resource', $keys)->whereNumber('id')->name('resource.edit');
        Route::put('/{resource}/{id}', [Admin\ResourceController::class, 'update'])->where('resource', $keys)->whereNumber('id')->name('resource.update');
        Route::delete('/{resource}/{id}', [Admin\ResourceController::class, 'destroy'])->where('resource', $keys)->whereNumber('id')->name('resource.destroy');
    });
});
