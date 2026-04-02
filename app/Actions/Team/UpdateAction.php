<?php

namespace App\Actions\Team;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\TeamMember;

class UpdateAction
{
	public function execute(TeamMember $member, array $data): TeamMember
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		$member->update($data);

		if (!empty($media)) {
			(new AttachMediaAction)->execute($media, $member);
		}

		return $member->fresh();
	}
}
