<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Glide\ServerFactory;
use League\Glide\Server;

class ImageController extends Controller
{
	protected Server $server;

	public function __construct()
	{
		$this->server = ServerFactory::create([
			'source' => storage_path('app/public'),
			'cache' => storage_path('app/.glide-cache'),
			'driver' => 'imagick',
		]);
	}

	public function show(Request $request, string $path): Response
	{
		$cachedPath = $this->server->makeImage($path, $request->all());
		$cache = $this->server->getCache();
		$imageContent = $cache->read($cachedPath);
		$mimeType = $this->resolveMimeType($request, $path);

		return response($imageContent, 200)
			->header('Content-Type', $mimeType)
			->header('Cache-Control', 'max-age=31536000, public')
			->header('Expires', now()->addYear()->toRfc7231String());
	}

	protected function resolveMimeType(Request $request, string $path): string
	{
		$format = strtolower((string) $request->query('fm', pathinfo($path, PATHINFO_EXTENSION)));

		return match ($format) {
			'avif' => 'image/avif',
			'webp' => 'image/webp',
			'png' => 'image/png',
			'gif' => 'image/gif',
			default => 'image/jpeg',
		};
	}
}
