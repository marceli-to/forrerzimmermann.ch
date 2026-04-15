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
    public ?array $desktopCrop;
    public ?array $mobileCrop;
    public float $aspectRatio;
    public float $mobileAspectRatio;
    public string $fit;
    public int $quality;
    public array $formats;
    public string $class;
    public string $loading;
    public string $sizes;
    public array $sources = [];
    public array $mobileSources = [];
    public string $fallbackUrl;
    public bool $hasResponsiveCrop;

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
        $this->fit = $fit;
        $this->quality = $quality;
        $this->formats = $formats;
        $this->class = $class;
        $this->loading = $loading;
        $this->sizes = $sizes;

        // Extract desktop/mobile crops from nested structure
        $this->desktopCrop = $media->crop['desktop'] ?? null;
        $this->mobileCrop = $media->crop['mobile'] ?? null;
        $this->hasResponsiveCrop = $this->mobileCrop !== null;

        // Desktop aspect ratio
        if ($this->desktopCrop && isset($this->desktopCrop['w'], $this->desktopCrop['h'])) {
            $this->aspectRatio = $this->desktopCrop['h'] / $this->desktopCrop['w'];
        } else {
            $baseWidth = $media->width ?? 1;
            $baseHeight = $media->height ?? 1;
            $this->aspectRatio = $baseHeight / $baseWidth;
        }

        // Mobile aspect ratio
        if ($this->mobileCrop && isset($this->mobileCrop['w'], $this->mobileCrop['h'])) {
            $this->mobileAspectRatio = $this->mobileCrop['h'] / $this->mobileCrop['w'];
        } else {
            $this->mobileAspectRatio = $this->aspectRatio;
        }

        // Largest width for img width/height attributes
        $widths = array_values(array_filter(self::WIDTHS, fn ($w) => $w <= $maxWidth));
        $this->width = end($widths) ?: $maxWidth;
        $this->height = (int) round($this->width * $this->aspectRatio);

        $this->buildSources($widths);
    }

    protected function buildSources(array $widths): void
    {
        if ($this->hasResponsiveCrop) {
            // Mobile sources (small widths only)
            $mobileWidths = array_values(array_filter($widths, fn ($w) => $w <= 768));
            foreach ($this->formats as $format) {
                if ($format === 'jpg' || $format === 'jpeg') {
                    continue;
                }
                $this->mobileSources[] = [
                    'srcset' => $this->buildSrcset($format, $mobileWidths, $this->mobileCrop, $this->mobileAspectRatio),
                    'type' => $this->getMimeType($format),
                    'sizes' => $this->sizes,
                    'media' => '(max-width: 767px)',
                ];
            }
        }

        // Desktop sources (with media query only when mobile crop exists)
        foreach ($this->formats as $format) {
            if ($format === 'jpg' || $format === 'jpeg') {
                continue;
            }

            $source = [
                'srcset' => $this->buildSrcset($format, $widths, $this->desktopCrop, $this->aspectRatio),
                'type' => $this->getMimeType($format),
                'sizes' => $this->sizes,
            ];

            if ($this->hasResponsiveCrop) {
                $source['media'] = '(min-width: 768px)';
            }

            $this->sources[] = $source;
        }

        $this->fallbackUrl = $this->buildUrl('jpg', $this->width, $this->height, $this->desktopCrop);
    }

    protected function buildSrcset(string $format, array $widths, ?array $crop, float $aspectRatio): string
    {
        $parts = [];
        foreach ($widths as $w) {
            $h = (int) round($w * $aspectRatio);
            $parts[] = $this->buildUrl($format, $w, $h, $crop) . ' ' . $w . 'w';
        }

        return implode(', ', $parts);
    }

    protected function buildUrl(string $format, int $width, int $height, ?array $crop = null): string
    {
        $params = [
            'w=' . $width,
            'h=' . $height,
            'fit=' . $this->fit,
        ];

        if ($crop && $this->fit === 'crop' && isset($crop['w'], $crop['h'], $crop['x'], $crop['y'])) {
            $params[] = 'crop=' . $crop['w'] . ',' . $crop['h'] . ',' . $crop['x'] . ',' . $crop['y'];
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
