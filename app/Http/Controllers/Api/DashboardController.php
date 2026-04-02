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

		$recentMedia = Media::orderByDesc('created_at')
			->limit(8)
			->get()
			->map(fn ($m) => [
				'uuid' => $m->uuid,
				'original_name' => $m->original_name,
				'thumbnail_url' => '/img/uploads/' . $m->file . '?w=200&h=200&fit=crop',
				'created_at' => $m->created_at,
			]);

		$recentProjects = Project::orderByDesc('created_at')
			->limit(5)
			->get()
			->map(fn ($p) => [
				'id' => $p->id,
				'title' => $p->title,
				'publish' => $p->publish,
				'created_at' => $p->created_at,
				'updated_at' => $p->updated_at,
			]);

		return response()->json([
			'stats' => [
				'projects_total' => $projects->count(),
				'projects_published' => $projects->where('publish', true)->count(),
				'projects_draft' => $projects->where('publish', false)->count(),
				'media_total' => $media->count(),
				'media_size' => $media->sum('size'),
			],
			'recent_projects' => $recentProjects,
			'recent_media' => $recentMedia,
		]);
	}
}
