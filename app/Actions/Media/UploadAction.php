<?php

namespace App\Actions\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class UploadAction
{
	public function execute(UploadedFile $file): array
	{
		$directory = 'temp';
		$filename = $this->uniqueFilename($file->getClientOriginalName());

		$file->storeAs($directory, $filename, 'public');

		$dimensions = $this->getDimensions($file);

		return [
			'uuid' => Str::uuid()->toString(),
			'file' => $filename,
			'original_name' => $file->getClientOriginalName(),
			'mime_type' => $file->getMimeType(),
			'size' => $file->getSize(),
			'width' => $dimensions[0] ?? null,
			'height' => $dimensions[1] ?? null,
			'alt' => null,
			'caption' => null,
			'is_teaser' => false,
			'sort_order' => 0,
			'orientation' => $this->orientation($dimensions[0] ?? null, $dimensions[1] ?? null),
			'type' => str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image',
			'original_url' => '/temp/' . $filename,
			'thumbnail_url' => '/img/temp/' . $filename . '?w=200&h=200&fit=crop',
			'preview_url' => '/img/temp/' . $filename . '?w=800&fit=max',
			'_temp' => true,
		];
	}

	private function getDimensions(UploadedFile $file): array
	{
		if (str_starts_with($file->getMimeType(), 'video/')) {
			return $this->getVideoDimensions($file->getRealPath());
		}

		$size = @getimagesize($file->getRealPath());

		return [$size[0] ?? null, $size[1] ?? null];
	}

	private function getVideoDimensions(string $path): array
	{
		$command = sprintf(
			'ffprobe -v error -select_streams v:0 -show_entries stream=width,height -of csv=s=x:p=0 %s',
			escapeshellarg($path)
		);

		$output = @shell_exec($command);

		if ($output && preg_match('/(\d+)x(\d+)/', trim($output), $matches)) {
			return [(int) $matches[1], (int) $matches[2]];
		}

		return [null, null];
	}

	private function uniqueFilename(string $originalName): string
	{
		$name = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
		$extension = Str::lower(pathinfo($originalName, PATHINFO_EXTENSION));
		$suffix = Str::random(6);

		return $name . '-' . $suffix . '.' . $extension;
	}

	private function orientation(?int $width, ?int $height): string
	{
		if (!$width || !$height) {
			return 'unknown';
		}
		if ($width > $height) {
			return 'landscape';
		}
		if ($height > $width) {
			return 'portrait';
		}
		return 'square';
	}
}
