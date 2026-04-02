<?php

namespace App\Actions\Content;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\Content;

class UpdateAction
{
	public function execute(Content $content, array $data): Content
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		$content->update($data);

		if (!empty($media)) {
			(new AttachMediaAction)->execute($media, $content);
		}

		return $content->fresh();
	}
}
