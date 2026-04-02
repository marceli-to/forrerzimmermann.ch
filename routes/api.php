<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\ProjectController;
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
				Route::get('/{project}', 'show');
				Route::put('/{project}', 'update');
				Route::patch('/{project}/publish', 'toggle');
				Route::delete('/{project}', 'destroy');
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
