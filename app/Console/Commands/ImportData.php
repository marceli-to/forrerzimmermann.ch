<?php

namespace App\Console\Commands;

use App\Models\Award;
use App\Models\Book;
use App\Models\Category;
use App\Models\CategoryType;
use App\Models\Content;
use App\Models\Job;
use App\Models\Lecture;
use App\Models\Media;
use App\Models\News;
use App\Models\Press;
use App\Models\Project;
use App\Models\ProjectGrid;
use App\Models\ProjectGridItem;
use App\Models\TeamMember;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportData extends Command
{
	protected $signature = 'app:import
		{--module= : Import a specific module (categories, projects, awards, books, content, jobs, lectures, news, press, team)}
		{--dry-run : Show what would be imported without making changes}';

	protected $description = 'Import data from the old website SQL dump and media files';

	protected string $importPath;
	protected string $mediaPath;
	protected bool $dryRun = false;
	protected array $stats = [];

	// Old ID → new ID mappings for cross-references
	protected array $categoryIdMap = [];
	protected array $categoryTypeIdMap = [];
	protected array $projectIdMap = [];

	protected array $modules = [
		'categories',
		'projects',
		'awards',
		'books',
		'content',
		'jobs',
		'lectures',
		'news',
		'press',
		'team',
	];

	public function handle(): void
	{
		$this->importPath = storage_path('app/private/import');
		$this->mediaPath = $this->importPath . '/media';
		$this->dryRun = $this->option('dry-run');

		if ($this->dryRun) {
			$this->warn('DRY RUN — no changes will be made.');
			$this->newLine();
		}

		$dumpFile = $this->importPath . '/dump.sql';
		if (!file_exists($dumpFile)) {
			$this->error('SQL dump not found at: ' . $dumpFile);
			return;
		}

		$module = $this->option('module');
		if ($module && !in_array($module, $this->modules)) {
			$this->error("Unknown module: {$module}. Available: " . implode(', ', $this->modules));
			return;
		}

		$sql = file_get_contents($dumpFile);
		$modulesToImport = $module ? [$module] : $this->modules;

		foreach ($modulesToImport as $mod) {
			$this->{"import" . ucfirst($mod)}($sql);
		}

		$this->newLine();
		$this->printSummary();
	}

	// ─── Categories ─────────────────────────────────────────────

	protected function importCategories(string $sql): void
	{
		$this->info('Importing categories...');
		$rows = $this->parseInserts($sql, 'categories');

		foreach ($rows as $i => $row) {
			$name = $this->unwrapJson($row['name']);

			$this->log("  Category: {$name}");

			if (!$this->dryRun) {
				$category = Category::create([
					'name' => $name,
					'publish' => (bool) $row['publish'],
					'sort_order' => $i,
				]);

				$this->categoryIdMap[(int) $row['id']] = $category->id;
			}

			$this->stat('categories');
		}

		// Import category types
		$typeRows = $this->parseInserts($sql, 'category_types');

		foreach ($typeRows as $i => $row) {
			$nameSingular = $this->unwrapJson($row['name_singular']);
			$namePlural = $this->unwrapJson($row['name_plural'] ?? null);

			$this->log("  Category type: {$nameSingular}");

			if (!$this->dryRun) {
				$newCategoryId = $this->categoryIdMap[(int) $row['category_id']] ?? null;

				if (!$newCategoryId) {
					$this->warn("    Skipping — category ID {$row['category_id']} not found");
					continue;
				}

				$type = CategoryType::create([
					'category_id' => $newCategoryId,
					'name' => $namePlural ?? $nameSingular,
					'name_singular' => $nameSingular,
					'publish' => (bool) $row['publish'],
					'sort_order' => max(0, (int) $row['order']),
				]);

				$this->categoryTypeIdMap[(int) $row['id']] = $type->id;
			}

			$this->stat('category_types');
		}
	}

	// ─── Projects ────────────────────────────────────────────────

	protected function importProjects(string $sql): void
	{
		$this->info('Importing projects...');

		// Ensure categories are imported first for ID mapping
		if (empty($this->categoryIdMap) && !$this->dryRun) {
			$this->warn('  Categories not imported yet — importing now...');
			$this->importCategories($sql);
		}

		$rows = $this->parseInserts($sql, 'projects');
		$imageRows = $this->parseInserts($sql, 'project_images');
		$fileRows = $this->parseInserts($sql, 'project_files');
		$gridRows = $this->parseInserts($sql, 'project_grids');
		$gridElementRows = $this->parseInserts($sql, 'project_grid_elements');
		$layoutRows = $this->parseInserts($sql, 'project_grid_layouts');

		// Build layout ID → key map
		$layoutKeyMap = [];
		foreach ($layoutRows as $row) {
			$layoutKeyMap[(int) $row['id']] = $row['key'];
		}

		foreach ($rows as $row) {
			$title = $this->unwrapJson($row['title']);
			$name = $this->unwrapJson($row['name']);
			$location = $this->unwrapJson($row['location']);
			$description = $this->unwrapJson($row['description']);
			$info = $this->unwrapJson($row['info']);
			$oldId = (int) $row['id'];

				$displayTitle = $title ?: ($name && $location ? "{$name}, {$location}" : ($name ?: "Project {$oldId}"));
			$this->log("  Project: {$displayTitle}");

			if (!$this->dryRun) {
				$newCategoryId = $this->categoryIdMap[(int) $row['category_id']] ?? null;
				$newCategoryTypeId = $this->categoryTypeIdMap[(int) $row['category_type_id']] ?? null;

				// Build unique slug from name + location
				$slugBase = Str::slug($name ?: $title ?: "project-{$oldId}");
				$slug = $slugBase;
				$suffix = 2;
				while (Project::where('slug', $slug)->exists()) {
					$slug = $slugBase . '-' . Str::slug($location ?? $suffix);
					$suffix++;
				}

				$resolvedTitle = $title
					?: ($name && $location ? "{$name}, {$location}" : ($name ?: "Project {$oldId}"));

				$project = Project::create([
					'category_id' => $newCategoryId,
					'category_type_id' => $newCategoryTypeId,
					'title' => $resolvedTitle,
					'slug' => $slug,
					'name' => $name,
					'location' => $location,
					'year' => (int) $row['year'] ?: null,
					'description' => $description,
					'info' => $info,
					'status' => $row['status'],
					'competition' => $row['competition'],
					'has_detail' => (bool) $row['has_detail'],
					'publish' => (bool) $row['publish'],
					'sort_order' => max(0, (int) $row['order']),
				]);

				$this->projectIdMap[$oldId] = $project->id;

				// Import project images as Media
				$projectImages = array_filter($imageRows, fn ($img) => (int) $img['project_id'] === $oldId);
				$oldImageIdToMediaId = [];
				$imageIndex = 0;

				foreach ($projectImages as $imgRow) {
					$media = $this->attachMediaWithOptions($project, $imgRow['name'], [
						'caption' => $this->unwrapJson($imgRow['caption'] ?? null),
						'is_teaser' => $imageIndex === 0,
						'sort_order' => $imageIndex,
					]);

					if ($media) {
						$oldImageIdToMediaId[(int) $imgRow['id']] = $media->id;
					}

					$imageIndex++;
				}

				// Import project files as Media
				$projectFiles = array_filter($fileRows, fn ($f) => (int) $f['project_id'] === $oldId);
				foreach ($projectFiles as $fileRow) {
					$this->attachMediaWithOptions($project, $fileRow['name'], [
						'caption' => $this->unwrapJson($fileRow['caption'] ?? null),
						'is_teaser' => false,
						'sort_order' => 999,
					]);
				}

				// Import project grids
				$projectGrids = array_filter($gridRows, fn ($g) => (int) $g['project_id'] === $oldId);

				foreach ($projectGrids as $gridRow) {
					$layoutKey = $layoutKeyMap[(int) $gridRow['layout_id']] ?? '2fr';
					$oldGridId = (int) $gridRow['id'];

					$grid = ProjectGrid::create([
						'project_id' => $project->id,
						'layout_key' => $layoutKey,
						'sort_order' => max(0, (int) $gridRow['order']),
					]);

					// Attach grid elements
					$gridElements = array_filter($gridElementRows, fn ($e) => (int) $e['grid_id'] === $oldGridId);

					foreach ($gridElements as $elementRow) {
						$mediaId = $oldImageIdToMediaId[(int) $elementRow['project_image_id']] ?? null;

						if ($mediaId) {
							ProjectGridItem::create([
								'project_grid_id' => $grid->id,
								'media_id' => $mediaId,
								'position' => (int) $elementRow['position'],
							]);
						}
					}
				}
			}

			$this->stat('projects');
		}
	}

	// ─── Awards ──────────────────────────────────────────────────

	protected function importAwards(string $sql): void
	{
		$this->info('Importing awards...');
		$rows = $this->parseInserts($sql, 'awards');

		foreach ($rows as $i => $row) {
			$title = $this->unwrapJson($row['title']);
			$description = $this->unwrapJson($row['description']);

			$this->log("  Award: {$title} ({$row['year']})");

			if (!$this->dryRun) {
				$award = Award::create([
					'title' => $title,
					'description' => $description,
					'year' => (string) $row['year'],
					'url' => $row['url'],
					'publish' => (bool) $row['publish'],
					'sort_order' => $i,
				]);

				$this->attachMedia($award, $row['media']);
			}

			$this->stat('awards');
		}
	}

	// ─── Books ───────────────────────────────────────────────────

	protected function importBooks(string $sql): void
	{
		$this->info('Importing books...');
		$rows = $this->parseInserts($sql, 'books');

		foreach ($rows as $row) {
			$title = $this->unwrapJson($row['title']);
			$description = $this->unwrapJson($row['description']);
			$info = $this->unwrapJson($row['info']);

			$this->log("  Book: {$title}");

			if (!$this->dryRun) {
				$book = Book::create([
					'title' => $title,
					'description' => $description,
					'info' => $info,
					'url' => $row['url'],
					'publish' => (bool) $row['publish'],
					'sort_order' => max(0, (int) $row['order']),
				]);

				$this->attachMedia($book, $row['media']);
			}

			$this->stat('books');
		}
	}

	// ─── Content ─────────────────────────────────────────────────

	protected function importContent(string $sql): void
	{
		$this->info('Importing content...');
		$rows = $this->parseInserts($sql, 'content');
		$contentImageRows = $this->parseInserts($sql, 'content_images');

		foreach ($rows as $i => $row) {
			$title = $this->unwrapJson($row['title']);
			$text = $this->unwrapJson($row['text']);

			$this->log("  Content: {$title} (key: {$row['key']})");

			if (!$this->dryRun) {
				$content = Content::create([
					'key' => $row['key'],
					'title' => $title,
					'text' => $text,
					'publish' => (bool) $row['publish'],
					'has_media' => (bool) $row['has_media'],
					'sort_order' => $i,
				]);

				// Attach content_images as media
				$images = array_filter($contentImageRows, fn ($img) => (int) $img['content_id'] === (int) $row['id']);
				foreach ($images as $imgRow) {
					$this->attachMedia($content, $imgRow['name']);
				}
			}

			$this->stat('content');
		}
	}

	// ─── Jobs ────────────────────────────────────────────────────

	protected function importJobs(string $sql): void
	{
		$this->info('Importing jobs...');
		$rows = $this->parseInserts($sql, 'jobs');

		foreach ($rows as $row) {
			$title = $this->unwrapJson($row['title']);
			$lead = $this->unwrapJson($row['lead']);
			$info = $this->unwrapJson($row['info']);

			$this->log("  Job: {$title} — {$lead}");

			if (!$this->dryRun) {
				$job = Job::create([
					'title' => $title,
					'lead' => $lead,
					'info' => $info,
					'publish' => (bool) $row['publish'],
					'sort_order' => max(0, (int) $row['order']),
				]);

				// Jobs use PDF media in old system
				$this->attachMedia($job, $row['media']);
			}

			$this->stat('jobs');
		}
	}

	// ─── Lectures ────────────────────────────────────────────────

	protected function importLectures(string $sql): void
	{
		$this->info('Importing lectures...');
		$rows = $this->parseInserts($sql, 'lectures');

		foreach ($rows as $i => $row) {
			$title = $this->unwrapJson($row['title']);
			$description = $this->unwrapJson($row['description']);

			$this->log("  Lecture: {$title} ({$row['year']})");

			if (!$this->dryRun) {
				$lecture = Lecture::create([
					'title' => $title,
					'description' => $description,
					'year' => (string) $row['year'],
					'publish' => (bool) $row['publish'],
					'sort_order' => $i,
				]);

				$this->attachMedia($lecture, $row['media']);
				$this->attachMedia($lecture, $row['file']);
			}

			$this->stat('lectures');
		}
	}

	// ─── News ────────────────────────────────────────────────────

	protected function importNews(string $sql): void
	{
		$this->info('Importing news...');
		$rows = $this->parseInserts($sql, 'news');

		foreach ($rows as $i => $row) {
			$title = $this->unwrapJson($row['title']);
			$subtitle = $this->unwrapJson($row['subtitle']);
			$text = $this->unwrapJson($row['text']);
			$date = $this->unwrapJson($row['date']);
			$link = $this->unwrapJson($row['link']);
			$linkText = $this->unwrapJson($row['linkText']);

			$this->log("  News: {$title}");

			if (!$this->dryRun) {
				$news = News::create([
					'date' => $date,
					'title' => $title,
					'subtitle' => $subtitle,
					'text' => $text,
					'link' => $link,
					'link_text' => $linkText,
					'sort_order' => $i,
				]);

				$this->attachMedia($news, $row['media']);
			}

			$this->stat('news');
		}
	}

	// ─── Press ───────────────────────────────────────────────────

	protected function importPress(string $sql): void
	{
		$this->info('Importing press...');
		$rows = $this->parseInserts($sql, 'press');

		foreach ($rows as $i => $row) {
			$title = $this->unwrapJson($row['title']);
			$description = $this->unwrapJson($row['description']);

			$this->log("  Press: {$title} ({$row['year']})");

			if (!$this->dryRun) {
				// Map old project_id to new if available
				$newProjectId = null;
				if ($row['project_id']) {
					$newProjectId = $this->projectIdMap[(int) $row['project_id']] ?? null;
				}

				$press = Press::create([
					'title' => $title,
					'description' => $description,
					'year' => (string) $row['year'],
					'url' => $row['url'],
					'project_id' => $newProjectId,
					'publish' => (bool) $row['publish'],
					'sort_order' => $i,
				]);

				$this->attachMedia($press, $row['media']);
				$this->attachMedia($press, $row['file']);
			}

			$this->stat('press');
		}
	}

	// ─── Team ────────────────────────────────────────────────────

	protected function importTeam(string $sql): void
	{
		$this->info('Importing team...');
		$rows = $this->parseInserts($sql, 'team');

		foreach ($rows as $row) {
			$role = $this->unwrapJson($row['role']);
			$position = $this->unwrapJson($row['position']);
			$cv = $this->unwrapJson($row['cv']);

			$this->log("  Team: {$row['firstname']} {$row['name']} ({$role})");

			if (!$this->dryRun) {
				$member = TeamMember::create([
					'firstname' => $row['firstname'],
					'name' => $row['name'],
					'role' => $role,
					'position' => $position,
					'phone' => $row['phone'],
					'email' => $row['email'],
					'cv' => $cv,
					'publish' => (bool) $row['publish'],
					'sort_order' => max(0, (int) $row['order']),
				]);

				$this->attachMedia($member, $row['media']);
			}

			$this->stat('team');
		}
	}

	// ─── SQL Parsing ─────────────────────────────────────────────

	protected function parseInserts(string $sql, string $table): array
	{
		$rows = [];
		$header = 'INSERT INTO `' . $table . '` (';
		$offset = 0;

		while (($pos = strpos($sql, $header, $offset)) !== false) {
			// Extract column names
			$colStart = $pos + strlen($header);
			$colEnd = strpos($sql, ')', $colStart);
			$columnStr = substr($sql, $colStart, $colEnd - $colStart);
			$columns = array_map(fn ($c) => trim($c, " `"), explode(',', $columnStr));

			// Find "VALUES" keyword after columns
			$valuesPos = strpos($sql, 'VALUES', $colEnd);
			if ($valuesPos === false) {
				$offset = $colEnd;
				continue;
			}

			// Parse tuples using state machine (handles semicolons inside strings)
			$cursor = $valuesPos + 6; // skip "VALUES"
			$len = strlen($sql);

			while ($cursor < $len) {
				// Skip whitespace/newlines/commas between tuples
				while ($cursor < $len && in_array($sql[$cursor], [' ', "\n", "\r", "\t", ','])) {
					$cursor++;
				}

				if ($cursor >= $len || $sql[$cursor] === ';') {
					break; // End of statement
				}

				if ($sql[$cursor] !== '(') {
					break; // Unexpected char, stop
				}

				// Parse one tuple: everything between ( and matching )
				$cursor++; // skip opening (
				$tuple = '';
				$inString = false;
				$escape = false;

				while ($cursor < $len) {
					$char = $sql[$cursor];

					if ($escape) {
						$tuple .= $char;
						$escape = false;
						$cursor++;
						continue;
					}

					if ($char === '\\' && $inString) {
						$tuple .= $char;
						$escape = true;
						$cursor++;
						continue;
					}

					if ($char === "'" && !$escape) {
						$inString = !$inString;
						$tuple .= $char;
						$cursor++;
						continue;
					}

					if ($char === ')' && !$inString) {
						$cursor++; // skip closing )
						break;
					}

					$tuple .= $char;
					$cursor++;
				}

				$values = $this->parseTupleValues($tuple);
				if (count($values) === count($columns)) {
					$rows[] = array_combine($columns, $values);
				}
			}

			$offset = $cursor;
		}

		if (empty($rows)) {
			$this->warn("  No data found for table: {$table}");
		}

		return $rows;
	}

	protected function parseTupleValues(string $tuple): array
	{
		$values = [];
		$current = '';
		$inString = false;
		$escape = false;

		for ($i = 0; $i < strlen($tuple); $i++) {
			$char = $tuple[$i];

			if ($escape) {
				$current .= $char;
				$escape = false;
				continue;
			}

			if ($char === '\\' && $inString) {
				$current .= $char;
				$escape = true;
				continue;
			}

			if ($char === "'" && !$escape) {
				$inString = !$inString;
				$current .= $char;
				continue;
			}

			if ($char === ',' && !$inString) {
				$values[] = $this->cleanValue(trim($current));
				$current = '';
				continue;
			}

			$current .= $char;
		}

		$values[] = $this->cleanValue(trim($current));

		return $values;
	}

	protected function cleanValue(string $value): ?string
	{
		if ($value === 'NULL') {
			return null;
		}

		// Strip surrounding quotes
		if (str_starts_with($value, "'") && str_ends_with($value, "'")) {
			$value = substr($value, 1, -1);
			// Unescape SQL escapes
			$value = str_replace("''", "'", $value);
			$value = str_replace("\\'", "'", $value);
			$value = stripslashes($value);
		}

		return $value;
	}

	// ─── JSON Unwrapping ─────────────────────────────────────────

	protected function unwrapJson(?string $value): ?string
	{
		if ($value === null || $value === '') {
			return null;
		}

		// Try to decode as JSON translation object
		$decoded = json_decode($value, true);
		if (is_array($decoded) && isset($decoded['de'])) {
			return $decoded['de'];
		}

		// Return as-is if not a JSON translation wrapper
		return $value;
	}

	// ─── Media Handling ──────────────────────────────────────────

	protected function attachMedia(Model $model, ?string $filename): void
	{
		if (!$filename || trim($filename) === '') {
			return;
		}

		$sourcePath = $this->findMediaFile($filename);

		if (!$sourcePath) {
			$this->warn("    Media not found: {$filename}");
			$this->stat('media_missing');
			return;
		}

		// Generate new filename for uploads
		$extension = Str::lower(pathinfo($filename, PATHINFO_EXTENSION));
		$newFilename = Str::random(12) . '.' . $extension;

		// Copy file to uploads
		$destination = Storage::disk('public')->path('uploads/' . $newFilename);
		copy($sourcePath, $destination);

		// Determine MIME type and dimensions
		$mimeType = mime_content_type($destination);
		$width = null;
		$height = null;

		if (str_starts_with($mimeType, 'image/')) {
			$imageSize = @getimagesize($destination);
			if ($imageSize) {
				$width = $imageSize[0];
				$height = $imageSize[1];
			}
		}

		$model->media()->create([
			'file' => $newFilename,
			'original_name' => $filename,
			'mime_type' => $mimeType,
			'size' => filesize($destination),
			'width' => $width,
			'height' => $height,
			'is_teaser' => true,
			'sort_order' => 0,
		]);

		$this->stat('media_imported');
	}

	protected function attachMediaWithOptions(Model $model, ?string $filename, array $options = []): ?Media
	{
		if (!$filename || trim($filename) === '') {
			return null;
		}

		$sourcePath = $this->findMediaFile($filename);

		if (!$sourcePath) {
			$this->warn("    Media not found: {$filename}");
			$this->stat('media_missing');
			return null;
		}

		$extension = Str::lower(pathinfo($filename, PATHINFO_EXTENSION));
		$newFilename = Str::random(12) . '.' . $extension;

		$destination = Storage::disk('public')->path('uploads/' . $newFilename);
		copy($sourcePath, $destination);

		$mimeType = mime_content_type($destination);
		$width = null;
		$height = null;

		if (str_starts_with($mimeType, 'image/')) {
			$imageSize = @getimagesize($destination);
			if ($imageSize) {
				$width = $imageSize[0];
				$height = $imageSize[1];
			}
		}

		$media = $model->media()->create([
			'file' => $newFilename,
			'original_name' => $filename,
			'mime_type' => $mimeType,
			'size' => filesize($destination),
			'width' => $width,
			'height' => $height,
			'caption' => $options['caption'] ?? null,
			'is_teaser' => $options['is_teaser'] ?? false,
			'sort_order' => $options['sort_order'] ?? 0,
		]);

		$this->stat('media_imported');

		return $media;
	}

	protected function findMediaFile(string $filename): ?string
	{
		// Search in root media folder first, then downloads/
		$paths = [
			$this->mediaPath . '/' . $filename,
			$this->mediaPath . '/downloads/' . $filename,
		];

		foreach ($paths as $path) {
			if (file_exists($path)) {
				return $path;
			}
		}

		return null;
	}

	// ─── Output & Stats ──────────────────────────────────────────

	protected function log(string $message): void
	{
		if ($this->dryRun) {
			$this->line("[DRY] {$message}");
		} else {
			$this->line($message);
		}
	}

	protected function stat(string $key): void
	{
		$this->stats[$key] = ($this->stats[$key] ?? 0) + 1;
	}

	protected function printSummary(): void
	{
		$this->info('─── Summary ───');

		if ($this->dryRun) {
			$this->warn('DRY RUN — nothing was changed.');
		}

		$statKeys = array_merge($this->modules, ['category_types']);
		foreach ($statKeys as $key) {
			if (isset($this->stats[$key])) {
				$this->line("  {$key}: {$this->stats[$key]} records");
			}
		}

		if (isset($this->stats['media_imported'])) {
			$this->line("  media copied: {$this->stats['media_imported']} files");
		}

		if (isset($this->stats['media_missing'])) {
			$this->warn("  media missing: {$this->stats['media_missing']} files");
		}
	}
}
