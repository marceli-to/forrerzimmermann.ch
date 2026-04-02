<?php

use App\Models\Category;
use App\Models\CategoryType;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists all categories with types', function () {
    $category = Category::factory()->create();
    CategoryType::factory()->count(2)->create(['category_id' => $category->id]);

    $this->actingAs($this->user)
        ->getJson('/api/dashboard/categories')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonCount(2, 'data.0.types');
});

it('creates a category', function () {
    $this->actingAs($this->user)
        ->postJson('/api/dashboard/categories', [
            'name' => 'Wohnbau',
            'publish' => true,
        ])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Wohnbau')
        ->assertJsonPath('data.publish', true);

    expect(Category::count())->toBe(1);
});

it('validates required fields on category create', function () {
    $this->actingAs($this->user)
        ->postJson('/api/dashboard/categories', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('updates a category', function () {
    $category = Category::factory()->create(['name' => 'Old Name']);

    $this->actingAs($this->user)
        ->putJson("/api/dashboard/categories/{$category->id}", [
            'name' => 'New Name',
            'publish' => true,
        ])
        ->assertOk()
        ->assertJsonPath('data.name', 'New Name');
});

it('toggles category publish state', function () {
    $category = Category::factory()->create(['publish' => true]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/categories/{$category->id}/publish")
        ->assertOk()
        ->assertJsonPath('data.publish', false);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/categories/{$category->id}/publish")
        ->assertOk()
        ->assertJsonPath('data.publish', true);
});

it('deletes a category', function () {
    $category = Category::factory()->create();

    $this->actingAs($this->user)
        ->deleteJson("/api/dashboard/categories/{$category->id}")
        ->assertNoContent();

    expect(Category::count())->toBe(0);
});

it('creates a type for a category', function () {
    $category = Category::factory()->create();

    $this->actingAs($this->user)
        ->postJson("/api/dashboard/categories/{$category->id}/types", [
            'name' => 'Mehrfamilienhäuser',
            'name_singular' => 'Mehrfamilienhaus',
            'publish' => true,
        ])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Mehrfamilienhäuser')
        ->assertJsonPath('data.name_singular', 'Mehrfamilienhaus');

    expect($category->types()->count())->toBe(1);
});

it('validates required fields on type create', function () {
    $category = Category::factory()->create();

    $this->actingAs($this->user)
        ->postJson("/api/dashboard/categories/{$category->id}/types", [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('updates a type', function () {
    $category = Category::factory()->create();
    $type = CategoryType::factory()->create(['category_id' => $category->id, 'name' => 'Old']);

    $this->actingAs($this->user)
        ->putJson("/api/dashboard/categories/{$category->id}/types/{$type->id}", [
            'name' => 'New',
            'name_singular' => 'New',
        ])
        ->assertOk()
        ->assertJsonPath('data.name', 'New');
});

it('deletes a type', function () {
    $category = Category::factory()->create();
    $type = CategoryType::factory()->create(['category_id' => $category->id]);

    $this->actingAs($this->user)
        ->deleteJson("/api/dashboard/categories/{$category->id}/types/{$type->id}")
        ->assertNoContent();

    expect($category->types()->count())->toBe(0);
});

it('reorders types', function () {
    $category = Category::factory()->create();
    $typeA = CategoryType::factory()->create(['category_id' => $category->id, 'sort_order' => 0]);
    $typeB = CategoryType::factory()->create(['category_id' => $category->id, 'sort_order' => 1]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/categories/{$category->id}/types/reorder", [
            'items' => [
                ['id' => $typeA->id, 'sort_order' => 1],
                ['id' => $typeB->id, 'sort_order' => 0],
            ],
        ])
        ->assertNoContent();

    expect($typeA->fresh()->sort_order)->toBe(1);
    expect($typeB->fresh()->sort_order)->toBe(0);
});

it('requires authentication', function () {
    $this->getJson('/api/dashboard/categories')
        ->assertUnauthorized();
});
