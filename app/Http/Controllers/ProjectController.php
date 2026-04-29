<?php

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectController extends Controller
{
	public function featured()
	{
		$projects = Project::published()
			->featured()
			->with('teaser')
			->orderBy('sort_order')
			->get();

		return view('pages.projects.index', compact('projects'));
	}

	public function worklist()
	{
		$projects = Project::published()
			->notFeatured()
			->orderByDesc('year')
			->orderBy('id')
			->get();

		return view('pages.projects.worklist', compact('projects'));
	}

	public function show(Project $project)
	{
		$project->load('media', 'topic');

		[$prev, $next] = $this->siblings($project);

		return view('pages.projects.show', compact('project', 'prev', 'next'));
	}

	protected function siblings(Project $project): array
	{
		$query = Project::published()->where('feature', $project->feature);

		if ($project->feature) {
			$query->orderBy('sort_order');
		} else {
			$query->orderByDesc('year')->orderBy('id');
		}

		$siblings = $query->get(['id', 'slug', 'title']);
		$count = $siblings->count();

		if ($count <= 1) {
			return [null, null];
		}

		$index = $siblings->search(fn ($p) => $p->id === $project->id);

		$prev = $siblings[($index - 1 + $count) % $count];
		$next = $siblings[($index + 1) % $count];

		return [$prev, $next];
	}
}
