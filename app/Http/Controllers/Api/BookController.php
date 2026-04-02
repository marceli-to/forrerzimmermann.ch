<?php

namespace App\Http\Controllers\Api;

use App\Actions\Book\DeleteAction as DeleteBookAction;
use App\Actions\Book\StoreAction as StoreBookAction;
use App\Actions\Book\UpdateAction as UpdateBookAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
	public function index()
	{
		$books = Book::with('media')->orderBy('sort_order')->get();

		return BookResource::collection($books);
	}

	public function store(StoreBookRequest $request)
	{
		$book = (new StoreBookAction)->execute($request->validated());

		return new BookResource($book->load('media'));
	}

	public function show(Book $book)
	{
		return new BookResource($book->load('media'));
	}

	public function update(UpdateBookRequest $request, Book $book)
	{
		$book = (new UpdateBookAction)->execute($book, $request->validated());

		return new BookResource($book->load('media'));
	}

	public function toggle(Book $book)
	{
		$book->update(['publish' => !$book->publish]);

		return new BookResource($book);
	}

	public function destroy(Book $book)
	{
		(new DeleteBookAction)->execute($book);

		return response()->json(null, 204);
	}

	public function reorder(Request $request)
	{
		foreach ($request->items as $item) {
			Book::find($item['id'])->update(['sort_order' => $item['sort_order']]);
		}

		return response()->json(['message' => 'Reordered']);
	}
}
