<?php

namespace App\Actions\Lecture;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\Lecture;

class UpdateAction
{
	public function execute(Lecture $lecture, array $data): Lecture
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		$lecture->update($data);

		if (!empty($media)) {
			(new AttachMediaAction)->execute($media, $lecture);
		}

		return $lecture->fresh();
	}
}
