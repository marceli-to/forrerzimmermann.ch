<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Project;

class DashboardController extends Controller
{
	public function index()
	{
		$projects = Project::all();
		$media = Media::all();

		$recentProjects = Project::orderByDesc('updated_at')
			->limit(5)
			->get()
			->map(fn ($p) => [
				'id' => $p->id,
				'title' => $p->title,
				'publish' => $p->publish,
				'updated_at' => $p->updated_at,
			]);

		return response()->json([
			'user' => auth()->user()->name,
			'stats' => [
				'projects_total' => $projects->count(),
				'projects_published' => $projects->where('publish', true)->count(),
				'projects_draft' => $projects->where('publish', false)->count(),
				'media_total' => $media->count(),
				'media_size' => $media->sum('size'),
			],
			'recent_projects' => $recentProjects,
		]);
	}
}
