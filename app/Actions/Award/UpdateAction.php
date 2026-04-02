<?php

namespace App\Actions\Award;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\Award;

class UpdateAction
{
	public function execute(Award $award, array $data): Award
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		$award->update($data);

		if (!empty($media)) {
			(new AttachMediaAction)->execute($media, $award);
		}

		return $award->fresh();
	}
}
