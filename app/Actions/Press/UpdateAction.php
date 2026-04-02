<?php

namespace App\Actions\Press;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\Press;

class UpdateAction
{
	public function execute(Press $press, array $data): Press
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		$press->update($data);

		if (!empty($media)) {
			(new AttachMediaAction)->execute($media, $press);
		}

		return $press->fresh();
	}
}
