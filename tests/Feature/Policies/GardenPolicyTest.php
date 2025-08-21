<?php

declare(strict_types=1);

use App\Models\Garden;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

describe('Garden Policy', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->garden = Garden::factory()->for($this->user)->create();
        $this->otherUserGarden = Garden::factory()->for($this->otherUser)->create();
    });

    describe('Admin Authorization (before method)', function () {
        it('allows admin to perform any action via before method', function () {
            // Create admin role if it doesn't exist
            Role::firstOrCreate(['name' => 'admin']);

            $admin = User::factory()->create();
            $admin->assignRole('admin');

            // Test various actions using Gate facade
            expect(Gate::forUser($admin)->allows('view', $this->garden))->toBeTrue()
                ->and(Gate::forUser($admin)->allows('view', $this->otherUserGarden))->toBeTrue()
                ->and(Gate::forUser($admin)->allows('update', $this->garden))->toBeTrue()
                ->and(Gate::forUser($admin)->allows('delete', $this->garden))->toBeTrue();
        });
    });

    describe('Owner Authorization', function () {
        it('allows garden owner to view their garden', function () {
            $response = Gate::forUser($this->user)->inspect('view', $this->garden);

            expect($response)->toBeInstanceOf(Response::class)
                ->and($response->allowed())->toBeTrue();
        });

        it('allows garden owner to update their garden', function () {
            $response = Gate::forUser($this->user)->inspect('update', $this->garden);

            expect($response)->toBeInstanceOf(Response::class)
                ->and($response->allowed())->toBeTrue();
        });

        it('allows garden owner to delete their garden', function () {
            $response = Gate::forUser($this->user)->inspect('delete', $this->garden);

            expect($response)->toBeInstanceOf(Response::class)
                ->and($response->allowed())->toBeTrue();
        });

        it('allows garden owner to restore their garden', function () {
            $response = Gate::forUser($this->user)->inspect('restore', $this->garden);

            expect($response)->toBeInstanceOf(Response::class)
                ->and($response->allowed())->toBeTrue();
        });

        it('allows garden owner to force delete their garden', function () {
            $response = Gate::forUser($this->user)->inspect('forceDelete', $this->garden);

            expect($response)->toBeInstanceOf(Response::class)
                ->and($response->allowed())->toBeTrue();
        });
    });

    describe('Non-Owner Authorization', function () {
        it('denies non-owner access to view other users gardens', function () {
            $response = Gate::forUser($this->user)->inspect('view', $this->otherUserGarden);

            expect($response)->toBeInstanceOf(Response::class)
                ->and($response->allowed())->toBeFalse()
                ->and($response->message())->toBe('Du kannst nur deine eigenen Gärten ansehen.');
        });

        it('denies non-owner access to update other users gardens', function () {
            $response = Gate::forUser($this->user)->inspect('update', $this->otherUserGarden);

            expect($response)->toBeInstanceOf(Response::class)
                ->and($response->allowed())->toBeFalse()
                ->and($response->message())->toBe('Du kannst nur deine eigenen Gärten bearbeiten.');
        });

        it('denies non-owner access to delete other users gardens', function () {
            $response = Gate::forUser($this->user)->inspect('delete', $this->otherUserGarden);

            expect($response)->toBeInstanceOf(Response::class)
                ->and($response->allowed())->toBeFalse()
                ->and($response->message())->toBe('Du kannst nur deine eigenen Gärten löschen.');
        });

        it('denies non-owner access to restore other users gardens', function () {
            $response = Gate::forUser($this->user)->inspect('restore', $this->otherUserGarden);

            expect($response)->toBeInstanceOf(Response::class)
                ->and($response->allowed())->toBeFalse()
                ->and($response->message())->toBe('Du kannst nur deine eigenen Gärten wiederherstellen.');
        });

        it('denies non-owner access to force delete other users gardens', function () {
            $response = Gate::forUser($this->user)->inspect('forceDelete', $this->otherUserGarden);

            expect($response)->toBeInstanceOf(Response::class)
                ->and($response->allowed())->toBeFalse()
                ->and($response->message())->toBe('Du kannst nur deine eigenen Gärten permanent löschen.');
        });
    });

    describe('General Permissions', function () {
        it('allows all users to view any gardens (viewAny)', function () {
            $response = Gate::forUser($this->user)->inspect('viewAny', Garden::class);

            expect($response)->toBeInstanceOf(Response::class)
                ->and($response->allowed())->toBeTrue();
        });

        it('allows all users to create gardens', function () {
            $response = Gate::forUser($this->user)->inspect('create', Garden::class);

            expect($response)->toBeInstanceOf(Response::class)
                ->and($response->allowed())->toBeTrue();
        });
    });

    describe('Policy Response Objects', function () {
        it('returns proper Response objects with allow status', function () {
            $response = Gate::forUser($this->user)->inspect('view', $this->garden);

            expect($response)->toBeInstanceOf(Response::class)
                ->and($response->allowed())->toBeTrue()
                ->and($response->denied())->toBeFalse();
        });

        it('returns proper Response objects with deny status and message', function () {
            $response = Gate::forUser($this->user)->inspect('view', $this->otherUserGarden);

            expect($response)->toBeInstanceOf(Response::class)
                ->and($response->allowed())->toBeFalse()
                ->and($response->denied())->toBeTrue()
                ->and($response->message())->toBeString()
                ->and($response->message())->not->toBeEmpty();
        });
    });

    describe('Gate Facade Usage', function () {
        it('works with Gate::allows() method', function () {
            expect(Gate::forUser($this->user)->allows('view', $this->garden))->toBeTrue()
                ->and(Gate::forUser($this->user)->allows('view', $this->otherUserGarden))->toBeFalse();
        });

        it('works with Gate::denies() method', function () {
            expect(Gate::forUser($this->user)->denies('view', $this->garden))->toBeFalse()
                ->and(Gate::forUser($this->user)->denies('view', $this->otherUserGarden))->toBeTrue();
        });

        it('throws exception when using Gate::authorize() for unauthorized access', function () {
            expect(fn () => Gate::forUser($this->user)->authorize('view', $this->otherUserGarden))
                ->toThrow(Illuminate\Auth\Access\AuthorizationException::class);
        });
    });
});
