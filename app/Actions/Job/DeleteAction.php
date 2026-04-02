<?php

namespace App\Actions\Job;

use App\Models\Job;
use Illuminate\Support\Facades\Storage;

class DeleteAction
{
	public function execute(Job $job): void
	{
		foreach ($job->media as $media) {
			Storage::disk('public')->delete('uploads/' . $media->file);
			$media->delete();
		}

		$job->delete();
	}
}
