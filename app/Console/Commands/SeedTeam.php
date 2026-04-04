<?php

namespace App\Console\Commands;

use App\Models\TeamMember;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeedTeam extends Command
{
    protected $signature = 'app:seed-team';
    protected $description = 'Seed team members with portrait images';

    protected array $members = [
        [
            'firstname' => 'Katrin',
            'name' => 'Zimmermann',
            'title' => 'Architektin MSc ETH',
            'email' => 'kzi@forrerzimmermann.ch',
            'sort_order' => 0,
            'image' => 'fza-dummy-team-1.jpg',
        ],
        [
            'firstname' => 'Stefan',
            'name' => 'Forrer',
            'title' => 'Architekt MSc SIA',
            'email' => 'sfo@forrerzimmermann.ch',
            'sort_order' => 1,
            'image' => 'fza-dummy-team-2.jpg',
        ],
        [
            'firstname' => 'Maya',
            'name' => 'Muster',
            'title' => 'Architektin MSc ETH',
            'email' => 'mmu@forrerzimmermann.ch',
            'sort_order' => 2,
            'image' => 'fza-dummy-team-3.jpg',
        ],
    ];

    public function handle(): void
    {
        foreach ($this->members as $data) {
            $member = TeamMember::firstOrCreate(
                ['firstname' => $data['firstname'], 'name' => $data['name']],
                [
                    'title' => $data['title'],
                    'email' => $data['email'],
                    'publish' => true,
                    'sort_order' => $data['sort_order'],
                ]
            );

            if ($member->media()->count() === 0) {
                $filename = $this->copyImage($data['image']);
                if ($filename) {
                    [$width, $height] = getimagesize(storage_path('app/public/uploads/' . $filename));
                    $member->media()->create([
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
        }

        $this->info('Team members seeded.');
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
