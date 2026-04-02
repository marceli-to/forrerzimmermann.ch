<?php

namespace App\Actions\CategoryType;

use App\Models\CategoryType;

class UpdateAction
{
	public function execute(CategoryType $type, array $data): CategoryType
	{
		$type->update($data);

		return $type;
	}
}
