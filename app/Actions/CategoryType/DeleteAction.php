<?php

namespace App\Actions\CategoryType;

use App\Models\CategoryType;

class DeleteAction
{
	public function execute(CategoryType $type): void
	{
		$type->delete();
	}
}
