<?php

namespace App\Actions\Category;

use App\Models\Category;

class DeleteAction
{
	public function execute(Category $category): void
	{
		$category->delete();
	}
}
