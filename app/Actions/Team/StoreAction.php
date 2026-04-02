<?php

namespace App\Actions\Team;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\TeamMember;

class StoreAction
{
	public function execute(array $data): TeamMember
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		$member = TeamMember::create($data);

		if (!empty($media)) {
			(new AttachMediaAction)->execute($media, $member);
		}

		return $member;
	}
}
