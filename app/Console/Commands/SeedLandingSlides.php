<?php

namespace App\Console\Commands;

use App\Models\LandingSlide;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeedLandingSlides extends Command
{
    protected $signature = 'app:seed-landing-slides';
    protected $description = 'Seed landing page slides';

    protected array $slides = [
        ['type' => 'image', 'image' => 'fza-dummy-1.jpg', 'text' => null],
        ['type' => 'image', 'image' => 'fza-dummy-2.jpg', 'text' => null],
        ['type' => 'image', 'image' => 'fza-dummy-3.jpg', 'text' => null],
        [
            'type' => 'image_text',
            'image' => 'fza-dummy-4.jpg',
            'text' => 'Wir entwickeln Architektur aus dem sorgfältigen Lesen von Ort und Bestand. Aus dieser Haltung entstehen Räume, die selbstverständlich wirken und langfristig tragen.',
        ],
    ];

    public function handle(): void
    {
        if (LandingSlide::count() > 0) {
            $this->info('Landing slides already seeded, skipping.');
            return;
        }

        foreach ($this->slides as $index => $data) {
            $slide = LandingSlide::create([
                'type' => $data['type'],
                'text' => $data['text'],
                'publish' => true,
                'sort_order' => $index,
            ]);

            $filename = $this->copyImage($data['image']);
            if ($filename) {
                [$width, $height] = getimagesize(storage_path('app/public/uploads/' . $filename));
                $slide->media()->create([
                    'file' => $filename,
                    'original_name' => $data['image'],
                    'mime_type' => 'image/jpeg',
                    'size' => File::size(storage_path('app/public/uploads/' . $filename)),
                    'width' => $width,
                    'height' => $height,
                    'sort_order' => 0,
                ]);
            }
        }

        $this->info('Landing slides seeded.');
    }

    private function copyImage(string $filename): ?string
    {
        $source = storage_path('app/seed/fza-images/' . $filename);
        if (!File::exists($source)) {
            $this->warn("Image not found: {$source}");
            return null;
        }

        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $unique = Str::uuid() . '.' . $ext;
        Storage::disk('public')->put('uploads/' . $unique, File::get($source));

        return $unique;
    }
}
