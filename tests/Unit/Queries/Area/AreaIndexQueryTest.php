<?php

declare(strict_types=1);
// tests/Integration/Queries/Area/AreaIndexQueryTest.php

use App\DTOs\Area\AreaIndexFilterDTO;
use App\Models\Area;
use App\Models\Garden;
use App\Models\User;
use App\Queries\Area\AreaIndexQuery;
use App\Repositories\Area\AreaRepository;

describe('AreaIndexQuery Integration', function () {

    beforeEach(function () {
        // Rollen erstellen für Tests
        Spatie\Permission\Models\Role::query()->firstOrCreate(['name' => 'admin']);
        Spatie\Permission\Models\Role::query()->firstOrCreate(['name' => 'user']);

        $this->query = new AreaIndexQuery(new AreaRepository());

        $this->user = User::factory()->user()->create();
        $this->garden = Garden::factory()->create(['user_id' => $this->user->id]);

        // Test-Areas erstellen
        Area::factory()->count(15)->create(['garden_id' => $this->garden->id]);
    });

    it('returns paginated results with 12 items per page', function () {
        $filter = new AreaIndexFilterDTO();

        $result = $this->query->execute($this->user->id, $filter, false);

        expect($result->count())->toBe(12)
            ->and($result->total())->toBe(15)
            ->and($result->hasPages())->toBeTrue();
    });
});
