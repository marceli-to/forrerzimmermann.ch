<?php

namespace App\Actions\Press;

use App\Models\Press;
use Illuminate\Support\Facades\Storage;

class DeleteAction
{
	public function execute(Press $press): void
	{
		foreach ($press->media as $media) {
			Storage::disk('public')->delete('uploads/' . $media->file);
			$media->delete();
		}

		$press->delete();
	}
}
