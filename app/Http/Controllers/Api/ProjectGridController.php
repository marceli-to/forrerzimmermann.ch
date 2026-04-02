<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectGridResource;
use App\Models\Project;
use App\Models\ProjectGrid;
use App\Models\ProjectGridItem;
use Illuminate\Http\Request;

class ProjectGridController extends Controller
{
	public function layouts()
	{
		return response()->json(['data' => config('grid-layouts')]);
	}

	public function index(Project $project)
	{
		$grids = $project->grids()->with('items.media')->get();

		return ProjectGridResource::collection($grids);
	}

	public function store(Request $request, Project $project)
	{
		$request->validate([
			'layout_key' => 'required|string',
		]);

		$maxOrder = $project->grids()->max('sort_order') ?? -1;

		$grid = $project->grids()->create([
			'layout_key' => $request->layout_key,
			'sort_order' => $maxOrder + 1,
		]);

		$grid->load('items.media');

		return new ProjectGridResource($grid);
	}

	public function destroy(Project $project, ProjectGrid $grid)
	{
		$grid->delete();

		return response()->json(null, 204);
	}

	public function reorder(Request $request, Project $project)
	{
		$request->validate([
			'items' => 'required|array',
			'items.*.id' => 'required|integer',
			'items.*.sort_order' => 'required|integer',
		]);

		foreach ($request->items as $item) {
			$project->grids()->where('id', $item['id'])->update([
				'sort_order' => $item['sort_order'],
			]);
		}

		return response()->json(['message' => 'ok']);
	}

	public function storeItem(Request $request, Project $project, ProjectGrid $grid)
	{
		$request->validate([
			'media_id' => 'required|integer|exists:media,id',
			'position' => 'required|integer',
		]);

		// Remove existing item at this position
		$grid->items()->where('position', $request->position)->delete();

		$item = $grid->items()->create([
			'media_id' => $request->media_id,
			'position' => $request->position,
		]);

		$item->load('media');

		return response()->json(['data' => [
			'id' => $item->id,
			'position' => $item->position,
			'media' => $item->media ? new \App\Http\Resources\MediaResource($item->media) : null,
		]]);
	}

	public function destroyItem(Project $project, ProjectGrid $grid, ProjectGridItem $item)
	{
		$item->delete();

		return response()->json(null, 204);
	}
}
