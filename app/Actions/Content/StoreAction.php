<?php

namespace App\Actions\Content;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\Content;

class StoreAction
{
	public function execute(array $data): Content
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		$content = Content::create($data);

		if (!empty($media)) {
			(new AttachMediaAction)->execute($media, $content);
		}

		return $content;
	}
}
