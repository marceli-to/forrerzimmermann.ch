<?php

namespace App\Http\Controllers\Api;

use App\Actions\Award\DeleteAction as DeleteAwardAction;
use App\Actions\Award\StoreAction as StoreAwardAction;
use App\Actions\Award\UpdateAction as UpdateAwardAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Award\StoreAwardRequest;
use App\Http\Requests\Award\UpdateAwardRequest;
use App\Http\Resources\AwardResource;
use App\Models\Award;

class AwardController extends Controller
{
	public function index()
	{
		$grouped = Award::with('media')
			->orderBy('year', 'desc')
			->get()
			->groupBy('year');

		return response()->json([
			'data' => $grouped->map(fn ($items) => AwardResource::collection($items)),
		]);
	}

	public function store(StoreAwardRequest $request)
	{
		$award = (new StoreAwardAction)->execute($request->validated());

		return new AwardResource($award->load('media'));
	}

	public function show(Award $award)
	{
		return new AwardResource($award->load('media'));
	}

	public function update(UpdateAwardRequest $request, Award $award)
	{
		$award = (new UpdateAwardAction)->execute($award, $request->validated());

		return new AwardResource($award->load('media'));
	}

	public function toggle(Award $award)
	{
		$award->update(['publish' => !$award->publish]);

		return new AwardResource($award);
	}

	public function destroy(Award $award)
	{
		(new DeleteAwardAction)->execute($award);

		return response()->json(null, 204);
	}
}
