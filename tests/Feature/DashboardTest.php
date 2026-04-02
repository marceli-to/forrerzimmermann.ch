<?php

use App\Models\Media;
use App\Models\Project;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('returns dashboard stats for authenticated user', function () {
    Project::factory()->count(3)->published()->create();
    Project::factory()->count(2)->create();

    $this->actingAs($this->user)
        ->getJson('/api/dashboard/')
        ->assertOk()
        ->assertJsonPath('stats.projects_total', 5)
        ->assertJsonPath('stats.projects_published', 3)
        ->assertJsonPath('stats.projects_draft', 2)
        ->assertJsonStructure([
            'stats' => ['projects_total', 'projects_published', 'projects_draft', 'media_total', 'media_size'],
            'recent_projects',
            'recent_media',
        ]);
});

it('requires authentication', function () {
    $this->getJson('/api/dashboard/')
        ->assertUnauthorized();
});
