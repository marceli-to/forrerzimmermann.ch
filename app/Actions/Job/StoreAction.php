<?php

namespace App\Actions\Job;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\Job;

class StoreAction
{
	public function execute(array $data): Job
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		$job = Job::create($data);

		if (!empty($media)) {
			(new AttachMediaAction)->execute($media, $job);
		}

		return $job;
	}
}
