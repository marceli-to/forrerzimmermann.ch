<?php

namespace App\Http\Controllers\Api;

use App\Actions\Project\DeleteAction as DeleteProjectAction;
use App\Actions\Project\StoreAction as StoreProjectAction;
use App\Actions\Project\UpdateAction as UpdateProjectAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProjectResource;
use App\Models\Category;
use App\Models\Project;

class ProjectController extends Controller
{
	public function index()
	{
		$projects = Project::with(['category', 'categoryType'])
			->orderBy('sort_order')
			->orderBy('created_at', 'desc')
			->get();

		return ProjectResource::collection($projects);
	}

	public function store(StoreProjectRequest $request)
	{
		$project = (new StoreProjectAction)->execute($request->validated());

		return new ProjectResource($project->load('category', 'categoryType'));
	}

	public function show(Project $project)
	{
		$project->load('category', 'categoryType', 'media', 'grids.items.media');

		return new ProjectResource($project);
	}

	public function update(UpdateProjectRequest $request, Project $project)
	{
		$project = (new UpdateProjectAction)->execute($project, $request->validated());

		return new ProjectResource($project->load('category', 'categoryType', 'media'));
	}

	public function toggle(Project $project)
	{
		$project->update(['publish' => !$project->publish]);

		return new ProjectResource($project);
	}

	public function destroy(Project $project)
	{
		(new DeleteProjectAction)->execute($project);

		return response()->json(null, 204);
	}

	public function categories()
	{
		$categories = Category::with('types')->orderBy('sort_order')->get();

		return CategoryResource::collection($categories);
	}
}
