<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\Topic;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeedProjects extends Command
{
    protected $signature = 'app:seed-projects';
    protected $description = 'Seed projects with dummy images';

    protected array $projects = [
        ['title' => 'Nietengasse', 'location' => 'Zürich', 'subtitle' => 'Instandsetzung', 'year' => 2024, 'images' => ['fza-dummy-1.jpg', 'fza-dummy-2.jpg', 'fza-dummy-3.jpg', 'fza-dummy-4.jpg']],
        ['title' => 'Musterstrasse', 'location' => 'Zürich', 'subtitle' => 'Strangsanierung', 'year' => 2024, 'images' => ['fza-dummy-5.jpg', 'fza-dummy-6.jpg', 'fza-dummy-7.jpg', 'fza-dummy-8.jpg', 'fza-dummy-9.jpg']],
        ['title' => 'Landwirtschaftsbetrieb Hagenbuchrain', 'location' => 'Zürich', 'subtitle' => '1. Rang, Planerwahlverfahren', 'year' => 2023, 'images' => ['fza-dummy-10.jpg', 'fza-dummy-11.jpg', 'fza-dummy-12.jpg', 'fza-dummy-1.jpg', 'fza-dummy-2.jpg', 'fza-dummy-3.jpg']],
        ['title' => 'Schulraumerweiterung Ebnet', 'location' => 'Abtwil', 'subtitle' => 'Projektwettbewerb auf Präqualifikation', 'year' => 2023, 'images' => ['fza-dummy-4.jpg', 'fza-dummy-5.jpg', 'fza-dummy-6.jpg', 'fza-dummy-7.jpg']],
        ['title' => 'Kindergarten Breiti', 'location' => 'Dietikon', 'subtitle' => 'Instandstellung und Anbau', 'year' => 2022, 'images' => ['fza-dummy-8.jpg', 'fza-dummy-9.jpg', 'fza-dummy-10.jpg', 'fza-dummy-11.jpg', 'fza-dummy-12.jpg']],
        ['title' => 'Mehrzweckraum & Turnhalle', 'location' => 'Flühli', 'subtitle' => 'Studienauftrag 2. Rang', 'year' => 2022, 'images' => ['fza-dummy-2.jpg', 'fza-dummy-4.jpg', 'fza-dummy-6.jpg', 'fza-dummy-8.jpg']],
        ['title' => 'Kindergarten Ennetbach Netstal', 'location' => 'Netstal', 'subtitle' => '3. Rang, offenes Planerwahlverfahren', 'year' => 2021, 'images' => ['fza-dummy-1.jpg', 'fza-dummy-3.jpg', 'fza-dummy-5.jpg', 'fza-dummy-7.jpg', 'fza-dummy-9.jpg']],
        ['title' => 'Stammhäuser GeHo', 'location' => 'Zürich', 'subtitle' => 'Studienwettbewerb auf Einladung', 'year' => 2021, 'images' => ['fza-dummy-10.jpg', 'fza-dummy-12.jpg', 'fza-dummy-2.jpg', 'fza-dummy-4.jpg', 'fza-dummy-6.jpg', 'fza-dummy-8.jpg']],
        ['title' => 'Schulhaus Borrweg', 'location' => 'Zürich', 'subtitle' => 'Offener Projektwettbewerb', 'year' => 2019, 'images' => ['fza-dummy-11.jpg', 'fza-dummy-1.jpg', 'fza-dummy-3.jpg', 'fza-dummy-5.jpg']],
        ['title' => 'Berufsschule Ziegelbrücke', 'location' => 'Ziegelbrücke', 'subtitle' => 'Offener Projektwettbewerb', 'year' => 2019, 'images' => ['fza-dummy-7.jpg', 'fza-dummy-9.jpg', 'fza-dummy-11.jpg', 'fza-dummy-2.jpg', 'fza-dummy-4.jpg']],
        ['title' => 'Forensikstation', 'location' => 'Will', 'subtitle' => 'Offener Projektwettbewerb', 'year' => 2019, 'images' => ['fza-dummy-6.jpg', 'fza-dummy-8.jpg', 'fza-dummy-10.jpg', 'fza-dummy-12.jpg']],
        ['title' => 'Marktplatz und Bohl', 'location' => 'St. Gallen', 'subtitle' => 'Offener Projektwettbewerb', 'year' => 2019, 'images' => ['fza-dummy-3.jpg', 'fza-dummy-5.jpg', 'fza-dummy-7.jpg', 'fza-dummy-9.jpg', 'fza-dummy-11.jpg', 'fza-dummy-1.jpg']],
    ];

    public function handle(): void
    {
        $topicIds = Topic::pluck('id')->toArray();

        foreach ($this->projects as $index => $data) {
            $project = Project::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    'title' => $data['title'],
                    'location' => $data['location'],
                    'subtitle' => $data['subtitle'],
                    'year' => $data['year'],
                    'publish' => true,
                    'sort_order' => $index,
                    'topic_id' => !empty($topicIds) ? $topicIds[array_rand($topicIds)] : null,
                ]
            );

            if ($project->media()->count() === 0) {
                foreach ($data['images'] as $sortOrder => $imageFile) {
                    $filename = $this->copyImage($imageFile);
                    if ($filename) {
                        [$width, $height] = getimagesize(storage_path('app/public/uploads/' . $filename));
                        $project->media()->create([
                            'file' => $filename,
                            'original_name' => $imageFile,
                            'mime_type' => 'image/jpeg',
                            'size' => File::size(storage_path('app/public/uploads/' . $filename)),
                            'width' => $width,
                            'height' => $height,
                            'is_teaser' => $sortOrder === 0,
                            'sort_order' => $sortOrder,
                        ]);
                    }
                }
            }
        }

        $this->info('Projects seeded.');
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
