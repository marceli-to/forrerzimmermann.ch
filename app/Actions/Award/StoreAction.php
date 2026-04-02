<?php

namespace App\Actions\Award;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\Award;

class StoreAction
{
	public function execute(array $data): Award
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		$award = Award::create($data);

		if (!empty($media)) {
			(new AttachMediaAction)->execute($media, $award);
		}

		return $award;
	}
}
