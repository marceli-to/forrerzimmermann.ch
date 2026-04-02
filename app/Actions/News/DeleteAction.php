<?php

namespace App\Actions\News;

use App\Models\News;
use Illuminate\Support\Facades\Storage;

class DeleteAction
{
	public function execute(News $news): void
	{
		foreach ($news->media as $media) {
			Storage::disk('public')->delete('uploads/' . $media->file);
			$media->delete();
		}

		$news->delete();
	}
}
