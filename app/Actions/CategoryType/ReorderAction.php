<?php

namespace App\Actions\CategoryType;

use App\Models\Category;

class ReorderAction
{
	public function execute(Category $category, array $items): void
	{
		foreach ($items as $item) {
			$category->types()->where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
		}
	}
}
