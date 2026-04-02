<?php

namespace App\Http\Controllers\Api;

use App\Actions\Press\DeleteAction as DeletePressAction;
use App\Actions\Press\StoreAction as StorePressAction;
use App\Actions\Press\UpdateAction as UpdatePressAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Press\StorePressRequest;
use App\Http\Requests\Press\UpdatePressRequest;
use App\Http\Resources\PressResource;
use App\Models\Press;

class PressController extends Controller
{
	public function index()
	{
		$grouped = Press::with('project', 'media')->orderBy('year', 'desc')->get()->groupBy('year');

		return response()->json([
			'data' => $grouped->map(fn ($items) => PressResource::collection($items)),
		]);
	}

	public function store(StorePressRequest $request)
	{
		$press = (new StorePressAction)->execute($request->validated());

		return new PressResource($press->load('project', 'media'));
	}

	public function show(Press $press)
	{
		return new PressResource($press->load('project', 'media'));
	}

	public function update(UpdatePressRequest $request, Press $press)
	{
		$press = (new UpdatePressAction)->execute($press, $request->validated());

		return new PressResource($press->load('project', 'media'));
	}

	public function toggle(Press $press)
	{
		$press->update(['publish' => !$press->publish]);

		return new PressResource($press);
	}

	public function destroy(Press $press)
	{
		(new DeletePressAction)->execute($press);

		return response()->json(null, 204);
	}
}
