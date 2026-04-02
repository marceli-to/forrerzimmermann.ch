<?php

namespace App\Actions\CategoryType;

use App\Models\Category;
use App\Models\CategoryType;

class StoreAction
{
	public function execute(Category $category, array $data): CategoryType
	{
		$data['category_id'] = $category->id;
		$data['sort_order'] = $category->types()->max('sort_order') + 1;

		return CategoryType::create($data);
	}
}
