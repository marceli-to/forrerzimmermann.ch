<?php

namespace App\View\Components\Media;

use App\Models\Media;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Image extends Component
{
    public string $src;
    public string $alt;
    public int $width;
    public int $height;
    public ?array $crop;
    public float $aspectRatio;
    public string $fit;
    public int $quality;
    public array $formats;
    public string $class;
    public string $loading;
    public string $sizes;
    public array $sources = [];
    public string $fallbackUrl;

    protected const WIDTHS = [480, 640, 768, 1024, 1280, 1440, 1600, 1920];

    public function __construct(
        Media $media,
        string $sizes = '100vw',
        int $maxWidth = 1600,
        ?string $alt = null,
        string $fit = 'crop',
        int $quality = 90,
        array $formats = ['avif', 'webp', 'jpg'],
        string $class = '',
        string $loading = 'lazy',
    ) {
        $this->src = 'uploads/' . $media->file;
        $this->alt = $alt ?? $media->alt ?? '';
        $this->crop = $media->crop;
        $this->fit = $fit;
        $this->quality = $quality;
        $this->formats = $formats;
        $this->class = $class;
        $this->loading = $loading;
        $this->sizes = $sizes;

        // Use crop dimensions for aspect ratio if crop exists, otherwise original
        if ($this->crop && isset($this->crop['w'], $this->crop['h'])) {
            $this->aspectRatio = $this->crop['h'] / $this->crop['w'];
        } else {
            $baseWidth = $media->width ?? 1;
            $baseHeight = $media->height ?? 1;
            $this->aspectRatio = $baseHeight / $baseWidth;
        }

        // Largest width for img width/height attributes
        $widths = array_values(array_filter(self::WIDTHS, fn ($w) => $w <= $maxWidth));
        $this->width = end($widths) ?: $maxWidth;
        $this->height = (int) round($this->width * $this->aspectRatio);

        $this->buildSources($widths);
    }

    protected function buildSources(array $widths): void
    {
        foreach ($this->formats as $format) {
            if ($format === 'jpg' || $format === 'jpeg') {
                continue;
            }

            $this->sources[] = [
                'srcset' => $this->buildSrcset($format, $widths),
                'type' => $this->getMimeType($format),
                'sizes' => $this->sizes,
            ];
        }

        $this->fallbackUrl = $this->buildUrl('jpg', $this->width, $this->height);
    }

    protected function buildSrcset(string $format, array $widths): string
    {
        $parts = [];
        foreach ($widths as $w) {
            $h = (int) round($w * $this->aspectRatio);
            $parts[] = $this->buildUrl($format, $w, $h) . ' ' . $w . 'w';
        }

        return implode(', ', $parts);
    }

    protected function buildUrl(string $format, int $width, int $height): string
    {
        $params = [
            'w=' . $width,
            'h=' . $height,
            'fit=' . $this->fit,
        ];

        if ($this->crop && $this->fit === 'crop' && isset($this->crop['w'], $this->crop['h'], $this->crop['x'], $this->crop['y'])) {
            $params[] = 'crop=' . $this->crop['w'] . ',' . $this->crop['h'] . ',' . $this->crop['x'] . ',' . $this->crop['y'];
        }

        $params[] = 'fm=' . $format;
        $params[] = 'q=' . $this->quality;

        return '/img/' . $this->src . '?' . implode('&', $params);
    }

    protected function getMimeType(string $format): string
    {
        return match ($format) {
            'avif' => 'image/avif',
            'webp' => 'image/webp',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            default => 'image/jpeg',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.media.image');
    }
}
