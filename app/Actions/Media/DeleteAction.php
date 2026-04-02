<?php

namespace App\Actions\Media;

use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class DeleteAction
{
	public function execute(Media $media): void
	{
		if ($media->mediable_id !== null) {
			throw new \RuntimeException('Media is in use and cannot be deleted.');
		}

		Storage::disk('public')->delete('uploads/' . $media->file);

		$media->delete();
	}
}
