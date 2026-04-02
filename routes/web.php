<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;

Route::get('/img/{path}', [ImageController::class, 'show'])->where('path', '.*');

Route::view('/', 'pages.landing')->name('page.landing');

// Dashboard (Vue SPA) — requires authentication
Route::middleware('auth')->group(function () {
	Route::get('/dashboard/{any?}', function () {
		return view('components.layout.app');
	})->where('any', '.*')->name('dashboard');
});

require __DIR__.'/auth.php';
