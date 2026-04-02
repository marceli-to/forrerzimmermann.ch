<?php

namespace App\Actions\Book;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\Book;

class UpdateAction
{
	public function execute(Book $book, array $data): Book
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		$book->update($data);

		if (!empty($media)) {
			(new AttachMediaAction)->execute($media, $book);
		}

		return $book->fresh();
	}
}
