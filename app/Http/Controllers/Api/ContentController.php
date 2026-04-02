<?php

namespace App\Http\Controllers\Api;

use App\Actions\Content\StoreAction as StoreContentAction;
use App\Actions\Content\UpdateAction as UpdateContentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Content\StoreContentRequest;
use App\Http\Requests\Content\UpdateContentRequest;
use App\Http\Resources\ContentResource;
use App\Models\Content;

class ContentController extends Controller
{
	public function index()
	{
		$content = Content::with('media')->orderBy('title')->get();

		return ContentResource::collection($content);
	}

	public function store(StoreContentRequest $request)
	{
		$content = (new StoreContentAction)->execute($request->validated());

		return new ContentResource($content->load('media'));
	}

	public function show(Content $content)
	{
		return new ContentResource($content->load('media'));
	}

	public function update(UpdateContentRequest $request, Content $content)
	{
		$content = (new UpdateContentAction)->execute($content, $request->validated());

		return new ContentResource($content->load('media'));
	}

	public function toggle(Content $content)
	{
		$content->update(['publish' => !$content->publish]);

		return new ContentResource($content);
	}
}
