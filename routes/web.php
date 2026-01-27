<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PrestasiController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Routes for the public-facing website. These routes handle subdomain
| routing to serve content for Universitas, Fakultas, and Prodi.
|
*/

// Routes with subdomain resolution middleware
Route::middleware(['resolve.unit', 'unit.published', 'unit.context'])->group(function () {
    // Home
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Articles / News
    Route::prefix('berita')->name('article.')->group(function () {
        Route::get('/', [ArticleController::class, 'index'])->name('index');
        Route::get('/kategori/{category:slug}', [ArticleController::class, 'category'])->name('category');
        Route::get('/{article:slug}', [ArticleController::class, 'show'])->name('show');
    });

    // Pages
    Route::get('/halaman/{page:slug}', [PageController::class, 'show'])->name('page.show');

    // Announcements / Pengumuman
    Route::prefix('pengumuman')->name('announcement.')->group(function () {
        Route::get('/', [AnnouncementController::class, 'index'])->name('index');
        Route::get('/{announcement}', [AnnouncementController::class, 'show'])->name('show');
    });

    // Prestasi / Achievements
    Route::prefix('prestasi')->name('prestasi.')->group(function () {
        Route::get('/', [PrestasiController::class, 'index'])->name('index');
        Route::get('/{prestasi}', [PrestasiController::class, 'show'])->name('show');
    });

    // Gallery
    Route::prefix('galeri')->name('gallery.')->group(function () {
        Route::get('/', [GalleryController::class, 'index'])->name('index');
        Route::get('/foto', [GalleryController::class, 'photos'])->name('photos');
        Route::get('/video', [GalleryController::class, 'videos'])->name('videos');
        Route::get('/{gallery}', [GalleryController::class, 'show'])->name('show');
    });

    // Events
    Route::prefix('agenda')->name('event.')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('index');
        Route::get('/{event}', [EventController::class, 'show'])->name('show');
    });

    // Downloads
    Route::prefix('unduhan')->name('download.')->group(function () {
        Route::get('/', [DownloadController::class, 'index'])->name('index');
        Route::get('/{download}', [DownloadController::class, 'download'])->name('file');
    });

    // Contact (with rate limiting)
    Route::prefix('kontak')->name('contact.')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::post('/', [ContactController::class, 'store'])
            ->middleware('throttle:5,1') // 5 requests per minute
            ->name('store');
    });

    // Dosen / Lecturers
    Route::prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/', [DosenController::class, 'index'])->name('index');
        Route::get('/{dosen:nidn}', [DosenController::class, 'show'])->name('show');
    });

    // Profile pages (static routes that map to pages)
    Route::prefix('profil')->name('profil.')->group(function () {
        Route::get('/visi-misi', [PageController::class, 'visiMisi'])->name('visi-misi');
        Route::get('/sejarah', [PageController::class, 'sejarah'])->name('sejarah');
        Route::get('/struktur', [PageController::class, 'struktur'])->name('struktur');
    });

    // Search
    Route::get('/cari', [HomeController::class, 'search'])->name('search');

    // SEO
    Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
    Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');
});

// Health check (no middleware needed)
Route::get('/ping', function () {
    return response()->json(['status' => 'ok']);
});

// Error page testing routes (only in non-production) - with unit context
if (app()->environment(['local', 'testing', 'development'])) {
    Route::middleware(['resolve.unit', 'unit.published', 'unit.context'])->group(function () {
        Route::get('/test-403', fn() => abort(403, 'Akses ditolak'));
        Route::get('/test-404', fn() => abort(404, 'Halaman tidak ditemukan'));
        Route::get('/test-500', fn() => abort(500, 'Server error'));
    });
}

/*
|--------------------------------------------------------------------------
| ⚠️ TESTING ROUTES - REMOVE BEFORE PRODUCTION ⚠️
|--------------------------------------------------------------------------
|
| These routes provide authentication bypass for testing purposes.
| They should be removed or commented out before deploying to production.
|
| @see docs/TODO.md for cleanup instructions
|
*/
if (app()->environment(['local', 'testing', 'development'])) {
    Route::prefix('_test')->name('test.')->group(function () {
        Route::get('/', [App\Http\Controllers\TestAuthController::class, 'testingDashboard'])->name('dashboard');
        Route::get('/login/superadmin', [App\Http\Controllers\TestAuthController::class, 'loginAsSuperAdmin'])->name('login.superadmin');
        Route::get('/login/admin', [App\Http\Controllers\TestAuthController::class, 'loginAsAdmin'])->name('login.admin');
        Route::get('/login/user/{userId}', [App\Http\Controllers\TestAuthController::class, 'loginAsUser'])->name('login.user');
        Route::get('/logout', [App\Http\Controllers\TestAuthController::class, 'logout'])->name('logout');
    });
}
