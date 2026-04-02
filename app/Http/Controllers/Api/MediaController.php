<?php

namespace App\Http\Controllers\Api;

use App\Actions\Media\DeleteAction as DeleteMediaAction;
use App\Actions\Media\ReorderAction as ReorderMediaAction;
use App\Actions\Media\SetTeaserAction;
use App\Actions\Media\UpdateAction as UpdateMediaAction;
use App\Actions\Media\UploadAction as UploadMediaAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Media\ReorderMediaRequest;
use App\Http\Requests\Media\UpdateMediaRequest;
use App\Http\Requests\Media\UploadMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;

class MediaController extends Controller
{
	public function index()
	{
		$media = Media::orderByDesc('created_at')->get();

		return MediaResource::collection($media);
	}

	public function upload(UploadMediaRequest $request)
	{
		$data = (new UploadMediaAction)->execute($request->file('file'));

		return response()->json(['data' => $data]);
	}

	public function update(UpdateMediaRequest $request, Media $media)
	{
		$media = (new UpdateMediaAction)->execute($media, $request->validated());

		return new MediaResource($media);
	}

	public function destroy(Media $media)
	{
		if ($media->mediable_id !== null) {
			return response()->json([
				'message' => 'Dieses Bild wird verwendet und kann nicht gelöscht werden.',
			], 422);
		}

		(new DeleteMediaAction)->execute($media);

		return response()->json(null, 204);
	}

	public function reorder(ReorderMediaRequest $request)
	{
		(new ReorderMediaAction)->execute($request->validated('items'));

		return response()->json(['message' => 'ok']);
	}

	public function teaser(Media $media)
	{
		$media = (new SetTeaserAction)->execute($media);

		return new MediaResource($media);
	}
}
