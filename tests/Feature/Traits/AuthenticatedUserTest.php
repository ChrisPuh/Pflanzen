<?php

declare(strict_types=1);

use App\Models\User;
use App\Traits\AuthenticatedUser;
use Spatie\Permission\Models\Role;

describe('AuthenticatedUser Trait', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()->create();
        $this->adminUser = User::factory()->create();

        // Create admin role if it doesn't exist
        Role::firstOrCreate(['name' => 'admin']);
        $this->adminUser->assignRole('admin');

        // Create a test class that uses the trait
        $this->testClass = new class
        {
            use AuthenticatedUser;

            public function testGetUserAndAdminStatus(): array
            {
                return $this->getUserAndAdminStatus();
            }

            public function testGetUser(): User
            {
                return $this->getUser();
            }

            public function testIsAdmin(): bool
            {
                return $this->isAdmin();
            }
        };
    });

    describe('getUserAndAdminStatus method', function (): void {
        it('returns user and admin status for regular user', function (): void {
            $this->actingAs($this->user);

            $result = $this->testClass->testGetUserAndAdminStatus();

            expect($result)->toBeArray()
                ->and($result)->toHaveKeys(['user', 'isAdmin'])
                ->and($result['user'])->toBeInstanceOf(User::class)
                ->and($result['user']->id)->toBe($this->user->id)
                ->and($result['isAdmin'])->toBeFalse();
        });

        it('returns user and admin status for admin user', function (): void {
            $this->actingAs($this->adminUser);

            $result = $this->testClass->testGetUserAndAdminStatus();

            expect($result)->toBeArray()
                ->and($result)->toHaveKeys(['user', 'isAdmin'])
                ->and($result['user'])->toBeInstanceOf(User::class)
                ->and($result['user']->id)->toBe($this->adminUser->id)
                ->and($result['isAdmin'])->toBeTrue();
        });
    });

    describe('getUser method', function (): void {
        it('returns authenticated user', function (): void {
            $this->actingAs($this->user);

            $result = $this->testClass->testGetUser();

            expect($result)->toBeInstanceOf(User::class)
                ->and($result->id)->toBe($this->user->id);
        });

        it('returns admin user when admin is authenticated', function (): void {
            $this->actingAs($this->adminUser);

            $result = $this->testClass->testGetUser();

            expect($result)->toBeInstanceOf(User::class)
                ->and($result->id)->toBe($this->adminUser->id);
        });
    });

    describe('isAdmin method', function (): void {
        it('returns false for regular user', function (): void {
            $this->actingAs($this->user);

            $result = $this->testClass->testIsAdmin();

            expect($result)->toBeFalse();
        });

        it('returns true for admin user', function (): void {
            $this->actingAs($this->adminUser);

            $result = $this->testClass->testIsAdmin();

            expect($result)->toBeTrue();
        });
    });

    describe('Integration with roles', function (): void {
        it('correctly identifies users without admin role', function (): void {
            $this->actingAs($this->user);

            expect($this->testClass->testIsAdmin())->toBeFalse()
                ->and($this->testClass->testGetUserAndAdminStatus()['isAdmin'])->toBeFalse();
        });

        it('correctly identifies users with admin role', function (): void {
            $this->actingAs($this->adminUser);

            expect($this->testClass->testIsAdmin())->toBeTrue()
                ->and($this->testClass->testGetUserAndAdminStatus()['isAdmin'])->toBeTrue();
        });

        it('handles role changes correctly', function (): void {
            $this->actingAs($this->user);

            // Initially not admin
            expect($this->testClass->testIsAdmin())->toBeFalse();

            // Grant admin role
            $this->user->assignRole('admin');
            $this->user->refresh();

            // Now should be admin
            expect($this->testClass->testIsAdmin())->toBeTrue();

            // Remove admin role
            $this->user->removeRole('admin');
            $this->user->refresh();

            // Should no longer be admin
            expect($this->testClass->testIsAdmin())->toBeFalse();
        });
    });
});
