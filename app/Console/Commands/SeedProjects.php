<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\CategoryType;
use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeedProjects extends Command
{
	protected $signature = 'app:projects';

	protected $description = 'Seed 25 dummy projects with images from storage/app/public/dummy';

	protected array $names = [
		'Wohnüberbauung Sonnenhof',
		'Schulhaus Neubühl',
		'Bürogebäude Westpark',
		'Gemeindehaus Allmend',
		'Wohnturm Bellevue',
		'Kulturzentrum Rösslimatt',
		'Sportanlage Grünau',
		'Kindergarten Bächli',
		'Alterszentrum Linde',
		'Bibliothek am Marktplatz',
		'Mehrfamilienhaus Kirchweg',
		'Gewerbehaus Industriestrasse',
		'Reihenhäuser Feldweg',
		'Primarschule Dorfmatt',
		'Wohnüberbauung Talacker',
		'Verwaltungsgebäude Zentrum',
		'Quartiertreff Schützenwiese',
		'Feuerwehrgebäude Nord',
		'Atelierhaus Kreuzplatz',
		'Gesundheitszentrum Bärenmatt',
		'Wohnüberbauung Rebgarten',
		'Laborgebäude Technopark',
		'Landschaftspark Seefeld',
		'Kirchgemeindehaus St. Peter',
		'Wohnsiedlung Eichrain',
	];

	protected array $locations = [
		'Zürich', 'Bern', 'Basel', 'Luzern', 'St. Gallen',
		'Winterthur', 'Biel/Bienne', 'Thun', 'Aarau', 'Frauenfeld',
		'Schaffhausen', 'Chur', 'Solothurn', 'Baden', 'Zug',
	];

	protected array $descriptions = [
		'Das Projekt entstand aus einem Wettbewerbsverfahren und reagiert auf die heterogene Umgebung mit einer klaren volumetrischen Setzung.',
		'Durch die sorgfältige Einbettung in die bestehende Quartierstruktur entsteht ein lebendiger Übergang zwischen öffentlichem und privatem Raum.',
		'Die Materialisierung in Sichtbeton und Holz verleiht dem Gebäude eine ruhige Präsenz im Strassenraum.',
		'Der Entwurf schafft grosszügige Aussenräume, die als Treffpunkt für die Nachbarschaft dienen.',
		'Die kompakte Gebäudeform ermöglicht eine effiziente Nutzung des Grundstücks bei gleichzeitig hoher Wohnqualität.',
	];

	public function handle(): void
	{
		$dummyFiles = collect(Storage::disk('public')->files('dummy'))
			->map(fn ($path) => basename($path))
			->values();

		if ($dummyFiles->isEmpty()) {
			$this->error('No dummy images found in storage/app/public/dummy/');
			return;
		}

		$categoryTypes = CategoryType::with('category')->get();

		if ($categoryTypes->isEmpty()) {
			$this->error('No category types found. Run app:categories first.');
			return;
		}

		$statuses = ['Ausgeführt', 'In Planung', 'Studie'];

		foreach ($this->names as $index => $name) {
			$categoryType = $categoryTypes->random();
			$location = $this->locations[array_rand($this->locations)];

			$project = Project::create([
				'category_id' => $categoryType->category_id,
				'category_type_id' => $categoryType->id,
				'title' => $name . ', ' . $location,
				'slug' => Str::slug($name),
				'name' => $name,
				'location' => $location,
				'year' => rand(2015, 2026),
				'description' => $this->descriptions[array_rand($this->descriptions)],
				'info' => fake()->optional(0.3)->sentence(10),
				'status' => $statuses[array_rand($statuses)],
				'competition' => fake()->optional(0.3)->sentence(3),
				'has_detail' => true,
				'publish' => true,
				'sort_order' => $index,
			]);

			$imageCount = rand(5, 10);
			$selectedImages = $dummyFiles->random($imageCount);

			foreach ($selectedImages as $sortOrder => $dummyFile) {
				$extension = Str::lower(pathinfo($dummyFile, PATHINFO_EXTENSION));
				$filename = Str::random(12) . '.' . $extension;

				Storage::disk('public')->copy('dummy/' . $dummyFile, 'uploads/' . $filename);

				$fullPath = Storage::disk('public')->path('uploads/' . $filename);
				$imageSize = @getimagesize($fullPath);

				$project->media()->create([
					'file' => $filename,
					'original_name' => $dummyFile,
					'mime_type' => $extension === 'png' ? 'image/png' : 'image/jpeg',
					'size' => Storage::disk('public')->size('uploads/' . $filename),
					'width' => $imageSize[0] ?? null,
					'height' => $imageSize[1] ?? null,
					'is_teaser' => $sortOrder === 0,
					'sort_order' => $sortOrder,
				]);
			}

			$this->info("Project \"{$name}\" created with {$imageCount} images.");
		}

		$this->info('Done! 25 projects seeded.');
	}
}
