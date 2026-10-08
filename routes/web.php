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
use Illuminate\Support\Facades\Storage;

// Public site
Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/proyek', [WorkController::class, 'index'])->name('works.index');
Route::get('/proyek/{work}', [WorkController::class, 'show'])->name('works.show');
Route::get('/tentang', [SiteController::class, 'about'])->name('about');
Route::get('/layanan', [SiteController::class, 'services'])->name('services');
Route::get('/layanan/{service}', [SiteController::class, 'service'])->name('services.show');
Route::get('/proses', [SiteController::class, 'process'])->name('process');
Route::get('/ruang', [SiteController::class, 'space'])->name('space');
Route::get('/catatan', [ThoughtController::class, 'index'])->name('thoughts.index');
Route::get('/catatan/{thought}', [ThoughtController::class, 'show'])->name('thoughts.show');
Route::get('/kontak', [ContactController::class, 'show'])->name('contact');
Route::get('/privasi', [LegalController::class, 'privacy'])->name('privacy');
Route::get('/ketentuan', [LegalController::class, 'terms'])->name('terms');
Route::post('/kontak', [ContactController::class, 'store'])->middleware('throttle:5,1,contact')->name('contact.store');

// The earlier English addresses keep working: permanent redirects (query strings kept), so links that were already
// shared, bookmarked or indexed are not lost. A form still open on an old page can also still be sent.
foreach ([
    '/works' => 'works.index', '/about' => 'about', '/services' => 'services', '/process' => 'process',
    '/space' => 'space', '/thoughts' => 'thoughts.index', '/contact' => 'contact', '/privacy' => 'privacy', '/terms' => 'terms',
] as $old => $name) {
    Route::get($old, fn (\Illuminate\Http\Request $request) => redirect()->route($name, $request->query(), 301));
}
Route::get('/works/{work}', fn (string $work) => redirect()->route('works.show', $work, 301));
Route::get('/services/{service}', fn (string $service) => redirect()->route('services.show', $service, 301));
Route::get('/thoughts/{thought}', fn (string $thought) => redirect()->route('thoughts.show', $thought, 301));
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1,contact');
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');
// Fallback for hosts where public/storage cannot be linked: serves uploaded pictures straight from the public disk.
// (When the symlink exists the web server answers first and this is never reached.)
Route::get('/storage/{path}', function (string $path) {
    $file = \App\Support\StorageSetup::locate($path);
    abort_unless($file, 404);

    return response()->file($file, ['Cache-Control' => 'public, max-age=31536000, immutable']);
})->where('path', '.*');

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
