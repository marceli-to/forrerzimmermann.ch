<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\CategoryType;
use Illuminate\Console\Command;

class SeedCategories extends Command
{
	protected $signature = 'app:categories';

	protected $description = 'Seed categories and category types for projects';

	protected array $data = [
		'Bauten' => [
			['name' => 'Wohnen',       'singular' => 'Wohnbaute'],
			['name' => 'Gewerbe',      'singular' => 'Gewerbebau'],
			['name' => 'Öffentlich',   'singular' => 'Öffentlicher Bau'],
			['name' => 'Industrie',    'singular' => 'Industriebau'],
			['name' => 'Kultur',       'singular' => 'Kulturbau'],
			['name' => 'Bildung',      'singular' => 'Bildungsbau'],
			['name' => 'Gesundheit',   'singular' => 'Gesundheitsbau'],
		],
		'Werke' => [
			['name' => 'Städtebau',    'singular' => 'Städtebau'],
			['name' => 'Landschaft',   'singular' => 'Landschaftsprojekt'],
			['name' => 'Innenausbau',  'singular' => 'Innenausbau'],
		],
	];

	public function handle(): void
	{
		foreach ($this->data as $categoryName => $types) {
			$category = Category::firstOrCreate(
				['name' => $categoryName],
				['publish' => true, 'sort_order' => 0]
			);

			foreach ($types as $index => $type) {
				CategoryType::firstOrCreate(
					['category_id' => $category->id, 'name' => $type['name']],
					[
						'name_singular' => $type['singular'],
						'publish' => true,
						'sort_order' => $index,
					]
				);
			}

			$this->info("Category \"{$categoryName}\" with " . count($types) . ' types seeded.');
		}

		$this->info('Done!');
	}
}
