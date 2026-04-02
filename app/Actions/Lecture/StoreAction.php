<?php

namespace App\Actions\Lecture;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\Lecture;

class StoreAction
{
	public function execute(array $data): Lecture
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		$lecture = Lecture::create($data);

		if (!empty($media)) {
			(new AttachMediaAction)->execute($media, $lecture);
		}

		return $lecture;
	}
}
