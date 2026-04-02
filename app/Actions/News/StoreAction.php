<?php

namespace App\Actions\News;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\News;

class StoreAction
{
	public function execute(array $data): News
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		$news = News::create($data);

		if (!empty($media)) {
			(new AttachMediaAction)->execute($media, $news);
		}

		return $news;
	}
}
