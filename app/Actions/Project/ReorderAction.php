<?php

namespace App\Actions\Project;

use App\Models\Project;

class ReorderAction
{
	public function execute(array $items): void
	{
		foreach ($items as $item) {
			Project::where('uuid', $item['uuid'])->update(['sort_order' => $item['sort_order']]);
		}
	}
}
