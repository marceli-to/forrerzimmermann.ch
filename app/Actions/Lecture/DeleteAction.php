<?php

namespace App\Actions\Lecture;

use App\Models\Lecture;
use Illuminate\Support\Facades\Storage;

class DeleteAction
{
	public function execute(Lecture $lecture): void
	{
		foreach ($lecture->media as $media) {
			Storage::disk('public')->delete('uploads/' . $media->file);
			$media->delete();
		}

		$lecture->delete();
	}
}
