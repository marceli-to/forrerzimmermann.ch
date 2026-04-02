<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AwardController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\LectureController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\PressController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProjectGridController;
use App\Http\Controllers\Api\TeamController;

Route::prefix('dashboard')
	->middleware(['web', 'auth'])
	->group(function () {

		Route::get('/', [DashboardController::class, 'index']);

		Route::controller(ProjectController::class)
			->prefix('projects')
			->group(function () {
				Route::get('/', 'index');
				Route::post('/', 'store');
				Route::get('/categories', 'categories');
				Route::get('/{project}', 'show');
				Route::put('/{project}', 'update');
				Route::patch('/{project}/publish', 'toggle');
				Route::delete('/{project}', 'destroy');
			});

		Route::controller(ProjectGridController::class)
			->prefix('projects/{project}/grids')
			->group(function () {
				Route::get('/layouts', 'layouts');
				Route::get('/', 'index');
				Route::post('/', 'store');
				Route::patch('/reorder', 'reorder');
				Route::delete('/{grid}', 'destroy');
				Route::post('/{grid}/items', 'storeItem');
				Route::delete('/{grid}/items/{item}', 'destroyItem');
			});

		Route::controller(CategoryController::class)
			->prefix('categories')
			->group(function () {
				Route::get('/', 'index');
				Route::post('/', 'store');
				Route::put('/{category}', 'update');
				Route::patch('/{category}/publish', 'toggle');
				Route::delete('/{category}', 'destroy');
				Route::post('/{category}/types', 'storeType');
				Route::patch('/{category}/types/reorder', 'reorderTypes');
				Route::put('/{category}/types/{type}', 'updateType');
				Route::delete('/{category}/types/{type}', 'destroyType');
			});

		Route::controller(MediaController::class)
			->prefix('media')
			->group(function () {
				Route::get('/', 'index');
				Route::post('/upload', 'upload');
				Route::put('/{media}', 'update');
				Route::delete('/{media}', 'destroy');
				Route::patch('/reorder', 'reorder');
				Route::patch('/{media}/teaser', 'teaser');
			});

		Route::controller(AwardController::class)
			->prefix('awards')
			->group(function () {
				Route::get('/', 'index');
				Route::post('/', 'store');
				Route::get('/{award}', 'show');
				Route::put('/{award}', 'update');
				Route::patch('/{award}/publish', 'toggle');
				Route::delete('/{award}', 'destroy');
			});

		Route::controller(BookController::class)
			->prefix('books')
			->group(function () {
				Route::get('/', 'index');
				Route::post('/', 'store');
				Route::patch('/reorder', 'reorder');
				Route::get('/{book}', 'show');
				Route::put('/{book}', 'update');
				Route::patch('/{book}/publish', 'toggle');
				Route::delete('/{book}', 'destroy');
			});

		Route::controller(ContentController::class)
			->prefix('content')
			->group(function () {
				Route::get('/', 'index');
				Route::post('/', 'store');
				Route::get('/{content}', 'show');
				Route::put('/{content}', 'update');
				Route::patch('/{content}/publish', 'toggle');
			});

		Route::controller(JobController::class)
			->prefix('jobs')
			->group(function () {
				Route::get('/', 'index');
				Route::post('/', 'store');
				Route::patch('/reorder', 'reorder');
				Route::get('/{job}', 'show');
				Route::put('/{job}', 'update');
				Route::patch('/{job}/publish', 'toggle');
				Route::delete('/{job}', 'destroy');
			});

		Route::controller(LectureController::class)
			->prefix('lectures')
			->group(function () {
				Route::get('/', 'index');
				Route::post('/', 'store');
				Route::get('/{lecture}', 'show');
				Route::put('/{lecture}', 'update');
				Route::patch('/{lecture}/publish', 'toggle');
				Route::delete('/{lecture}', 'destroy');
			});

		Route::controller(NewsController::class)
			->prefix('news')
			->group(function () {
				Route::get('/', 'index');
				Route::post('/', 'store');
				Route::get('/{news}', 'show');
				Route::put('/{news}', 'update');
				Route::delete('/{news}', 'destroy');
			});

		Route::controller(PressController::class)
			->prefix('press')
			->group(function () {
				Route::get('/', 'index');
				Route::post('/', 'store');
				Route::get('/{press}', 'show');
				Route::put('/{press}', 'update');
				Route::patch('/{press}/publish', 'toggle');
				Route::delete('/{press}', 'destroy');
			});

		Route::controller(TeamController::class)
			->prefix('team')
			->group(function () {
				Route::get('/', 'index');
				Route::post('/', 'store');
				Route::patch('/reorder', 'reorder');
				Route::get('/{team}', 'show');
				Route::put('/{team}', 'update');
				Route::patch('/{team}/publish', 'toggle');
				Route::delete('/{team}', 'destroy');
			});

	});
