<?php

namespace App\Actions\Project;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\Project;
use Illuminate\Support\Str;

class UpdateAction
{
	public function execute(Project $project, array $data): Project
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		$data['slug'] = Str::slug($data['title']);

		$project->update($data);

		if (!empty($media)) {
			(new AttachMediaAction)->execute($media, $project);
		}

		return $project->fresh();
	}
}
