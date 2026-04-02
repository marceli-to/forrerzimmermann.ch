<?php

namespace App\Actions\Book;

use App\Models\Book;
use Illuminate\Support\Facades\Storage;

class DeleteAction
{
	public function execute(Book $book): void
	{
		foreach ($book->media as $media) {
			Storage::disk('public')->delete('uploads/' . $media->file);
			$media->delete();
		}

		$book->delete();
	}
}
