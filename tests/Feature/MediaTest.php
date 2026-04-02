<?php

use App\Models\Media;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::factory()->create();
    Storage::fake('public');
});

it('lists all media', function () {
    Media::factory()->count(5)->create();

    $this->actingAs($this->user)
        ->getJson('/api/dashboard/media')
        ->assertOk()
        ->assertJsonCount(5, 'data');
});

it('uploads an image to temp storage', function () {
    $file = UploadedFile::fake()->image('photo.jpg', 800, 600);

    $response = $this->actingAs($this->user)
        ->postJson('/api/dashboard/media/upload', ['file' => $file])
        ->assertOk()
        ->assertJsonStructure(['data' => ['uuid', 'file', 'original_name', 'width', 'height']])
        ->assertJsonPath('data._temp', true);

    $filename = $response->json('data.file');
    Storage::disk('public')->assertExists('temp/' . $filename);
});

it('rejects non-image uploads', function () {
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $this->actingAs($this->user)
        ->postJson('/api/dashboard/media/upload', ['file' => $file])
        ->assertUnprocessable();
});

it('updates media alt and caption', function () {
    $media = Media::factory()->create(['alt' => '', 'caption' => '']);

    $this->actingAs($this->user)
        ->putJson("/api/dashboard/media/{$media->uuid}", [
            'alt' => 'A nice photo',
            'caption' => 'Taken in Zurich',
        ])
        ->assertOk()
        ->assertJsonPath('data.alt', 'A nice photo')
        ->assertJsonPath('data.caption', 'Taken in Zurich');
});

it('prevents deletion of attached media', function () {
    $media = Media::factory()->create();

    $this->actingAs($this->user)
        ->deleteJson("/api/dashboard/media/{$media->uuid}")
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Dieses Bild wird verwendet und kann nicht gelöscht werden.');

    expect(Media::count())->toBe(1);
});

it('deletes unattached media', function () {
    Storage::disk('public')->put('uploads/test.jpg', 'fake');
    $media = Media::factory()->unattached()->create(['file' => 'test.jpg']);

    $this->actingAs($this->user)
        ->deleteJson("/api/dashboard/media/{$media->uuid}")
        ->assertNoContent();

    expect(Media::count())->toBe(0);
    Storage::disk('public')->assertMissing('uploads/test.jpg');
});

it('toggles teaser on', function () {
    $project = Project::factory()->create();
    $media = Media::factory()->create([
        'mediable_type' => Project::class,
        'mediable_id' => $project->id,
        'is_teaser' => false,
    ]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/media/{$media->uuid}/teaser")
        ->assertOk()
        ->assertJsonPath('data.is_teaser', true);
});

it('toggles teaser off', function () {
    $project = Project::factory()->create();
    $media = Media::factory()->create([
        'mediable_type' => Project::class,
        'mediable_id' => $project->id,
        'is_teaser' => true,
    ]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/media/{$media->uuid}/teaser")
        ->assertOk()
        ->assertJsonPath('data.is_teaser', false);
});

it('requires authentication for media', function () {
    $this->getJson('/api/dashboard/media')
        ->assertUnauthorized();
});
