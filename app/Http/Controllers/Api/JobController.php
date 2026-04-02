<?php

namespace App\Http\Controllers\Api;

use App\Actions\Job\DeleteAction as DeleteJobAction;
use App\Actions\Job\StoreAction as StoreJobAction;
use App\Actions\Job\UpdateAction as UpdateJobAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Job\StoreJobRequest;
use App\Http\Requests\Job\UpdateJobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
	public function index()
	{
		$jobs = Job::with('media')->orderBy('sort_order')->get();

		return JobResource::collection($jobs);
	}

	public function store(StoreJobRequest $request)
	{
		$job = (new StoreJobAction)->execute($request->validated());

		return new JobResource($job->load('media'));
	}

	public function show(Job $job)
	{
		return new JobResource($job->load('media'));
	}

	public function update(UpdateJobRequest $request, Job $job)
	{
		$job = (new UpdateJobAction)->execute($job, $request->validated());

		return new JobResource($job->load('media'));
	}

	public function toggle(Job $job)
	{
		$job->update(['publish' => !$job->publish]);

		return new JobResource($job);
	}

	public function destroy(Job $job)
	{
		(new DeleteJobAction)->execute($job);

		return response()->json(null, 204);
	}

	public function reorder(Request $request)
	{
		foreach ($request->items as $item) {
			Job::find($item['id'])->update(['sort_order' => $item['sort_order']]);
		}

		return response()->json(['message' => 'Reordered']);
	}
}
