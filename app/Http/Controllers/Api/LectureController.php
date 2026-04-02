<?php

namespace App\Http\Controllers\Api;

use App\Actions\Lecture\DeleteAction as DeleteLectureAction;
use App\Actions\Lecture\StoreAction as StoreLectureAction;
use App\Actions\Lecture\UpdateAction as UpdateLectureAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lecture\StoreLectureRequest;
use App\Http\Requests\Lecture\UpdateLectureRequest;
use App\Http\Resources\LectureResource;
use App\Models\Lecture;

class LectureController extends Controller
{
	public function index()
	{
		$grouped = Lecture::with('media')->orderBy('year', 'desc')->get()->groupBy('year');

		return response()->json([
			'data' => $grouped->map(fn ($items) => LectureResource::collection($items)),
		]);
	}

	public function store(StoreLectureRequest $request)
	{
		$lecture = (new StoreLectureAction)->execute($request->validated());

		return new LectureResource($lecture->load('media'));
	}

	public function show(Lecture $lecture)
	{
		return new LectureResource($lecture->load('media'));
	}

	public function update(UpdateLectureRequest $request, Lecture $lecture)
	{
		$lecture = (new UpdateLectureAction)->execute($lecture, $request->validated());

		return new LectureResource($lecture->load('media'));
	}

	public function toggle(Lecture $lecture)
	{
		$lecture->update(['publish' => !$lecture->publish]);

		return new LectureResource($lecture);
	}

	public function destroy(Lecture $lecture)
	{
		(new DeleteLectureAction)->execute($lecture);

		return response()->json(null, 204);
	}
}
