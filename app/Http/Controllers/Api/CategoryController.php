<?php

namespace App\Http\Controllers\Api;

use App\Actions\Category\DeleteAction as DeleteCategoryAction;
use App\Actions\Category\StoreAction as StoreCategoryAction;
use App\Actions\Category\UpdateAction as UpdateCategoryAction;
use App\Actions\CategoryType\DeleteAction as DeleteCategoryTypeAction;
use App\Actions\CategoryType\ReorderAction as ReorderCategoryTypeAction;
use App\Actions\CategoryType\StoreAction as StoreCategoryTypeAction;
use App\Actions\CategoryType\UpdateAction as UpdateCategoryTypeAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Requests\CategoryType\ReorderCategoryTypeRequest;
use App\Http\Requests\CategoryType\StoreCategoryTypeRequest;
use App\Http\Requests\CategoryType\UpdateCategoryTypeRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryTypeResource;
use App\Models\Category;
use App\Models\CategoryType;

class CategoryController extends Controller
{
	public function index()
	{
		$categories = Category::with('types')->orderBy('sort_order')->get();

		return CategoryResource::collection($categories);
	}

	public function store(StoreCategoryRequest $request)
	{
		$category = (new StoreCategoryAction)->execute($request->validated());

		return new CategoryResource($category->load('types'));
	}

	public function update(UpdateCategoryRequest $request, Category $category)
	{
		$category = (new UpdateCategoryAction)->execute($category, $request->validated());

		return new CategoryResource($category->load('types'));
	}

	public function toggle(Category $category)
	{
		$category = (new UpdateCategoryAction)->execute($category, ['publish' => !$category->publish]);

		return new CategoryResource($category->load('types'));
	}

	public function destroy(Category $category)
	{
		(new DeleteCategoryAction)->execute($category);

		return response()->json(null, 204);
	}

	public function storeType(StoreCategoryTypeRequest $request, Category $category)
	{
		$type = (new StoreCategoryTypeAction)->execute($category, $request->validated());

		return new CategoryTypeResource($type);
	}

	public function updateType(UpdateCategoryTypeRequest $request, Category $category, CategoryType $type)
	{
		$type = (new UpdateCategoryTypeAction)->execute($type, $request->validated());

		return new CategoryTypeResource($type);
	}

	public function destroyType(Category $category, CategoryType $type)
	{
		(new DeleteCategoryTypeAction)->execute($type);

		return response()->json(null, 204);
	}

	public function reorderTypes(ReorderCategoryTypeRequest $request, Category $category)
	{
		(new ReorderCategoryTypeAction)->execute($category, $request->validated('items'));

		return response()->json(null, 204);
	}
}
