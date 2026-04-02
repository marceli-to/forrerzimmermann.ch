<?php

use App\Models\Project;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists all projects', function () {
    Project::factory()->count(3)->create();

    $this->actingAs($this->user)
        ->getJson('/api/dashboard/projects')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

it('creates a project', function () {
    $this->actingAs($this->user)
        ->postJson('/api/dashboard/projects', [
            'title' => 'New Project',
            'publish' => false,
        ])
        ->assertCreated()
        ->assertJsonPath('data.title', 'New Project');

    expect(Project::count())->toBe(1);
});

it('validates required fields on create', function () {
    $this->actingAs($this->user)
        ->postJson('/api/dashboard/projects', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title']);
});

it('shows a single project', function () {
    $project = Project::factory()->create(['title' => 'My Project']);

    $this->actingAs($this->user)
        ->getJson("/api/dashboard/projects/{$project->id}")
        ->assertOk()
        ->assertJsonPath('data.title', 'My Project');
});

it('updates a project', function () {
    $project = Project::factory()->create(['title' => 'Old Title']);

    $this->actingAs($this->user)
        ->putJson("/api/dashboard/projects/{$project->id}", [
            'title' => 'New Title',
            'publish' => true,
        ])
        ->assertOk()
        ->assertJsonPath('data.title', 'New Title');
});

it('toggles publish state', function () {
    $project = Project::factory()->create(['publish' => false]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/projects/{$project->id}/publish")
        ->assertOk()
        ->assertJsonPath('data.publish', true);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/projects/{$project->id}/publish")
        ->assertOk()
        ->assertJsonPath('data.publish', false);
});

it('deletes a project', function () {
    $project = Project::factory()->create();

    $this->actingAs($this->user)
        ->deleteJson("/api/dashboard/projects/{$project->id}")
        ->assertNoContent();

    expect(Project::count())->toBe(0);
});

it('requires authentication', function () {
    $this->getJson('/api/dashboard/projects')
        ->assertUnauthorized();
});
