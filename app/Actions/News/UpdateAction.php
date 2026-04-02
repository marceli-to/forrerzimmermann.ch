<?php

namespace App\Actions\News;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\News;

class UpdateAction
{
	public function execute(News $news, array $data): News
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		$news->update($data);

		if (!empty($media)) {
			(new AttachMediaAction)->execute($media, $news);
		}

		return $news->fresh();
	}
}
