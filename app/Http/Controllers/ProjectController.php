<?php

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectController extends Controller
{
	public function index()
	{
		$projects = Project::published()
			->where('feature', true)
			->with(['teaser', 'desktopTeaser', 'mobileTeaser'])
			->orderBy('sort_order')
			->get();

		return view('pages.projects.index', compact('projects'));
	}

	public function worklist()
	{
		$projects = Project::published()
			->orderByDesc('year')
			->get();

		return view('pages.projects.worklist', compact('projects'));
	}

	public function show($slug)
	{
		return view('pages.projects.show');
	}
}
