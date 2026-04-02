<?php

namespace App\Actions\Award;

use App\Models\Award;
use Illuminate\Support\Facades\Storage;

class DeleteAction
{
	public function execute(Award $award): void
	{
		foreach ($award->media as $media) {
			Storage::disk('public')->delete('uploads/' . $media->file);
			$media->delete();
		}

		$award->delete();
	}
}
