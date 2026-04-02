<?php

namespace App\Actions\Press;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\Press;

class StoreAction
{
	public function execute(array $data): Press
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		$press = Press::create($data);

		if (!empty($media)) {
			(new AttachMediaAction)->execute($media, $press);
		}

		return $press;
	}
}
