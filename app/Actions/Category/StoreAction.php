<?php

namespace App\Actions\Category;

use App\Models\Category;

class StoreAction
{
	public function execute(array $data): Category
	{
		$data['sort_order'] = Category::max('sort_order') + 1;

		return Category::create($data);
	}
}
