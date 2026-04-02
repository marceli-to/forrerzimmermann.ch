<?php

namespace App\Actions\Book;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\Book;

class StoreAction
{
	public function execute(array $data): Book
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		$book = Book::create($data);

		if (!empty($media)) {
			(new AttachMediaAction)->execute($media, $book);
		}

		return $book;
	}
}
