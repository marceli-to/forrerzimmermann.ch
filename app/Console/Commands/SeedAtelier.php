<?php

namespace App\Console\Commands;

use App\Models\AtelierPage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeedAtelier extends Command
{
    protected $signature = 'app:seed-atelier';
    protected $description = 'Seed atelier pages';

    protected array $images = [
        'fza-dummy-1.jpg', 'fza-dummy-2.jpg', 'fza-dummy-3.jpg',
        'fza-dummy-4.jpg', 'fza-dummy-5.jpg', 'fza-dummy-6.jpg',
        'fza-dummy-7.jpg', 'fza-dummy-8.jpg', 'fza-dummy-9.jpg',
        'fza-dummy-10.jpg', 'fza-dummy-11.jpg', 'fza-dummy-12.jpg',
    ];

    public function handle(): void
    {
        $profilText = "Wir sind ein junges, engagiertes und dynamisches Architekturbüro mit Sitz in der Stadt Zürich. Nach langjähriger Mitarbeit bei renommierten Architekten gründeten wir, Katrin Zimmermann und Stefan Forrer, unser eigenes Büro in Zürich. Seit 2018 erarbeiten wir architektonische Lösungsvorschläge als Beiträge bei Wettbewerben, Studien und Planerwahlverfahren.\n\nDa wir auf umfassende Erfahrungen in der Projektierung und Ausführung von ganz kleinen bis zu sehr grossen Bauvorhaben zurückgreifen können, sind wir in der Lage, gezielt und effizient auf unterschiedliche Aufgabenstellungen und Ortsgegebenheiten einzugehen.";

        $pages = [
            ['slug' => 'profile', 'title' => 'Atelier', 'text' => $profilText],
            ['slug' => 'team', 'title' => null, 'text' => null],
            ['slug' => 'jobs', 'title' => null, 'text' => null],
        ];

        foreach ($pages as $data) {
            $page = AtelierPage::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'title' => $data['title'],
                    'text' => $data['text'],
                    'publish' => true,
                ]
            );

            if ($page->media()->count() === 0) {
                $imageFile = $this->images[array_rand($this->images)];
                $filename = $this->copyImage($imageFile);
                if ($filename) {
                    [$width, $height] = getimagesize(storage_path('app/public/uploads/' . $filename));
                    $page->media()->create([
                        'file' => $filename,
                        'original_name' => $imageFile,
                        'mime_type' => 'image/jpeg',
                        'size' => File::size(storage_path('app/public/uploads/' . $filename)),
                        'width' => $width,
                        'height' => $height,
                        'sort_order' => 0,
                    ]);
                }
            }
        }

        $this->info('Atelier pages seeded.');
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
