<?php

namespace App\Http\Controllers;

use App\Support\ImageSupport;
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
		$params = $request->all();
		if (isset($params['fm']) && !ImageSupport::supports((string) $params['fm'])) {
			unset($params['fm']);
		}

		$cachedPath = $this->server->makeImage($path, $params);
		$cache = $this->server->getCache();
		$imageContent = $cache->read($cachedPath);
		$mimeType = $this->resolveMimeType($request, $path, $params);

		return response($imageContent, 200)
			->header('Content-Type', $mimeType)
			->header('Cache-Control', 'max-age=31536000, public')
			->header('Expires', now()->addYear()->toRfc7231String());
	}

	protected function resolveMimeType(Request $request, string $path, array $params): string
	{
		$format = strtolower((string) ($params['fm'] ?? pathinfo($path, PATHINFO_EXTENSION)));

		return match ($format) {
			'avif' => 'image/avif',
			'webp' => 'image/webp',
			'png' => 'image/png',
			'gif' => 'image/gif',
			default => 'image/jpeg',
		};
	}
}
