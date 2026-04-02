<?php

namespace App\Actions\Job;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\Job;

class UpdateAction
{
	public function execute(Job $job, array $data): Job
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		$job->update($data);

		if (!empty($media)) {
			(new AttachMediaAction)->execute($media, $job);
		}

		return $job->fresh();
	}
}
