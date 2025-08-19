<?php

declare(strict_types=1);

use App\Models\Area;
use App\Models\Garden;
use App\Models\User;
use App\Policies\AreaPolicy;

it('allows user to view their own area', function (): void {
    $user = User::factory()->create();
    $garden = Garden::factory()->for($user)->create();
    $area = Area::factory()->for($garden)->create();

    $policy = new AreaPolicy();

    expect($policy->view($user, $area)->allowed())->toBeTrue();
});

it('denies user from viewing others area', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $garden = Garden::factory()->for($user2)->create();
    $area = Area::factory()->for($garden)->create();

    $policy = new AreaPolicy();

    expect($policy->view($user1, $area)->allowed())->toBeFalse()
        ->and($policy->view($user1, $area)->message())->toBe('Du kannst nur Bereiche deiner eigenen Gärten ansehen.');
});

it('allows user to update their own area', function (): void {
    $user = User::factory()->create();
    $garden = Garden::factory()->for($user)->create();
    $area = Area::factory()->for($garden)->create();

    $policy = new AreaPolicy();

    expect($policy->update($user, $area)->allowed())->toBeTrue();
});

it('denies user from updating others area', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $garden = Garden::factory()->for($user2)->create();
    $area = Area::factory()->for($garden)->create();

    $policy = new AreaPolicy();

    expect($policy->update($user1, $area)->allowed())->toBeFalse()
        ->and($policy->update($user1, $area)->message())->toBe('Du kannst nur Bereiche deiner eigenen Gärten bearbeiten.');
});

it('allows user to delete their own area', function (): void {
    $user = User::factory()->create();
    $garden = Garden::factory()->for($user)->create();
    $area = Area::factory()->for($garden)->create();

    $policy = new AreaPolicy();

    expect($policy->delete($user, $area)->allowed())->toBeTrue();
});

it('denies user from deleting others area', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $garden = Garden::factory()->for($user2)->create();
    $area = Area::factory()->for($garden)->create();

    $policy = new AreaPolicy();

    expect($policy->delete($user1, $area)->allowed())->toBeFalse()
        ->and($policy->delete($user1, $area)->message())->toBe('Du kannst nur Bereiche deiner eigenen Gärten löschen.');
});

it('allows user to restore their own area', function (): void {
    $user = User::factory()->create();
    $garden = Garden::factory()->for($user)->create();
    $area = Area::factory()->for($garden)->create();

    $policy = new AreaPolicy();

    expect($policy->restore($user, $area)->allowed())->toBeTrue();
});

it('denies user from restoring others area', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $garden = Garden::factory()->for($user2)->create();
    $area = Area::factory()->for($garden)->create();

    $policy = new AreaPolicy();

    expect($policy->restore($user1, $area)->allowed())->toBeFalse()
        ->and($policy->restore($user1, $area)->message())->toBe('Du kannst nur Bereiche deiner eigenen Gärten wiederherstellen.');
});

it('allows user to force delete their own area', function (): void {
    $user = User::factory()->create();
    $garden = Garden::factory()->for($user)->create();
    $area = Area::factory()->for($garden)->create();

    $policy = new AreaPolicy();

    expect($policy->forceDelete($user, $area)->allowed())->toBeTrue();
});

it('denies user from force deleting others area', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $garden = Garden::factory()->for($user2)->create();
    $area = Area::factory()->for($garden)->create();

    $policy = new AreaPolicy();

    expect($policy->forceDelete($user1, $area)->allowed())->toBeFalse()
        ->and($policy->forceDelete($user1, $area)->message())->toBe('Du kannst nur Bereiche deiner eigenen Gärten permanent löschen.');
});

it('allows any authenticated user to create areas', function (): void {
    $user = User::factory()->create();

    $policy = new AreaPolicy();

    expect($policy->create($user)->allowed())->toBeTrue();
});

it('allows any authenticated user to view any areas list', function (): void {
    $policy = new AreaPolicy();

    expect($policy->viewAny()->allowed())->toBeTrue();
});
