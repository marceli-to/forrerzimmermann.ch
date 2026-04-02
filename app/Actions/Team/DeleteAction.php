<?php

namespace App\Actions\Team;

use App\Models\TeamMember;
use Illuminate\Support\Facades\Storage;

class DeleteAction
{
	public function execute(TeamMember $member): void
	{
		foreach ($member->media as $media) {
			Storage::disk('public')->delete('uploads/' . $media->file);
			$media->delete();
		}

		$member->delete();
	}
}
