<?php

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectController extends Controller
{
	public function featured()
	{
		$projects = Project::published()
			->where('feature', true)
			->with('teaser')
			->orderBy('sort_order')
			->get();

		return view('pages.projects.index', compact('projects'));
	}

	public function worklist()
	{
		$projects = Project::published()
			->where('feature', false)
			->orderByDesc('year')
			->get();

		return view('pages.projects.worklist', compact('projects'));
	}

	public function show($slug)
	{
		return view('pages.projects.show');
	}
}
