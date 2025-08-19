<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property \App\Enums\PlantCategoryEnum $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Plant> $plants
 * @property-read int|null $plants_count
 * @method static \Database\Factories\CategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUpdatedAt($value)
 */
	final class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property GardenTypeEnum $type
 * @property string|null $location
 * @property float|null $size_sqm
 * @property string|null $description
 * @property array|null $coordinates
 * @property string|null $postal_code
 * @property string|null $city
 * @property string $country
 * @property bool $is_active
 * @property Carbon|null $established_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $user
 * @property-read Plant[] $plants
 * @property-read int|null $age_in_years
 * @property-read string $formatted_size
 * @property-read string $full_location
 * @property-read int|null $plants_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Garden active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Garden byLocation(string $location)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Garden byType(\App\Enums\Garden\GardenTypeEnum $type)
 * @method static \Database\Factories\GardenFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Garden forUser(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Garden newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Garden newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Garden query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Garden whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Garden whereCoordinates($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Garden whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Garden whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Garden whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Garden whereEstablishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Garden whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Garden whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Garden whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Garden whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Garden wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Garden whereSizeSqm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Garden whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Garden whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Garden whereUserId($value)
 */
	final class Garden extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $latin_name
 * @property string|null $description
 * @property int $plant_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \App\Models\PlantType $plantType
 * @method static \Database\Factories\PlantFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant whereLatinName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant wherePlantTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant whereUpdatedAt($value)
 */
	final class Plant extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property \App\Enums\PlantTypeEnum $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Plant> $plants
 * @property-read int|null $plants_count
 * @method static \Database\Factories\PlantTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantType whereUpdatedAt($value)
 */
	final class PlantType extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Garden> $gardens
 * @property-read int|null $gardens_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	final class User extends \Eloquent implements \Filament\Models\Contracts\FilamentUser {}
}

