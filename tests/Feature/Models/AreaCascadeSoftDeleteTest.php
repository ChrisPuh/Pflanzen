<?php

declare(strict_types=1);

use App\Models\Area;
use App\Models\Garden;
use App\Models\User;

it('cascades soft delete when garden is deleted', function (): void {
    $user = User::factory()->create();
    $garden = Garden::factory()->for($user)->create();
    $areas = Area::factory()->for($garden)->count(3)->create();

    expect(Area::count())->toBe(3)
        ->and(Garden::count())->toBe(1);

    $garden->delete();

    expect(Garden::count())->toBe(0)
        ->and(Area::count())->toBe(0)
        ->and(Garden::withTrashed()->count())->toBe(1)
        ->and(Area::withTrashed()->count())->toBe(3);

    $areas->each(function (Area $area): void {
        $area->refresh();
        expect($area->deleted_at)->not->toBeNull();
    });
});

it('restores areas when garden is restored', function (): void {
    $user = User::factory()->create();
    $garden = Garden::factory()->for($user)->create();
    $areas = Area::factory()->for($garden)->count(2)->create();

    $garden->delete();

    expect(Garden::count())->toBe(0)
        ->and(Area::count())->toBe(0);

    $garden->restore();

    expect(Garden::count())->toBe(1)
        ->and(Area::count())->toBe(2);

    $areas->each(function (Area $area): void {
        $area->refresh();
        expect($area->deleted_at)->toBeNull();
    });
});

it('does not affect areas from other gardens when one garden is deleted', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $garden1 = Garden::factory()->for($user1)->create();
    $garden2 = Garden::factory()->for($user2)->create();

    $garden1Areas = Area::factory()->for($garden1)->count(2)->create();
    $garden2Areas = Area::factory()->for($garden2)->count(3)->create();

    expect(Area::count())->toBe(5);

    $garden1->delete();

    expect(Garden::count())->toBe(1)
        ->and(Area::count())->toBe(3);

    $garden1Areas->each(function (Area $area): void {
        $area->refresh();
        expect($area->deleted_at)->not->toBeNull();
    });

    $garden2Areas->each(function (Area $area): void {
        $area->refresh();
        expect($area->deleted_at)->toBeNull();
    });
});

it('can permanently delete areas when garden is force deleted', function (): void {
    $user = User::factory()->create();
    $garden = Garden::factory()->for($user)->create();
    $areas = Area::factory()->for($garden)->count(2)->create();

    $garden->delete();
    $garden->forceDelete();

    expect(Garden::withTrashed()->count())->toBe(0)
        ->and(Area::withTrashed()->count())->toBe(0);
});

it('handles multiple cascade levels correctly', function (): void {
    $user = User::factory()->create();
    $garden = Garden::factory()->for($user)->create();
    $areas = Area::factory()->for($garden)->count(2)->create();

    expect($garden->areas)->toHaveCount(2);

    $garden->delete();

    expect(Garden::withTrashed()->first()->areas()->withTrashed()->count())->toBe(2);

    $garden->restore();

    expect($garden->areas()->count())->toBe(2);
});

it('works with mixed active and inactive areas', function (): void {
    $user = User::factory()->create();
    $garden = Garden::factory()->for($user)->create();

    $activeAreas = Area::factory()->for($garden)->active()->count(2)->create();
    $inactiveAreas = Area::factory()->for($garden)->inactive()->count(1)->create();

    expect(Area::count())->toBe(3)
        ->and(Area::active()->count())->toBe(2);

    $garden->delete();

    expect(Area::count())->toBe(0)
        ->and(Area::withTrashed()->count())->toBe(3);

    $garden->restore();

    expect(Area::count())->toBe(3)
        ->and(Area::active()->count())->toBe(2);
});
