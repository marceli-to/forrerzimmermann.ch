<?php

namespace App\Actions\Team;

use App\Actions\Media\DeleteAction as DeleteMediaAction;
use App\Models\TeamMember;

class DeleteAction
{
	public function execute(TeamMember $member): void
	{
		$deleteMedia = new DeleteMediaAction;

		foreach ($member->media as $media) {
			$deleteMedia->execute($media);
		}

		$member->delete();
	}
}
