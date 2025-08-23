<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Garden\GardenTypeEnum;
use Carbon\Carbon;
use Database\Factories\GardenFactory;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
 * @property-read Area[] $areas
 */
final class Garden extends Model
{
    /** @use HasFactory<GardenFactory> */
    use CascadeSoftDeletes, HasFactory, SoftDeletes;

    protected array $cascadeDeletes = ['areas'];

    protected $table = 'gardens';

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'location',
        'size_sqm',
        'description',
        'coordinates',
        'postal_code',
        'city',
        'country',
        'is_active',
        'established_at',
    ];

    protected $casts = [
        'name' => 'string',
        'type' => GardenTypeEnum::class,
        'location' => 'string',
        'size_sqm' => 'float',
        'description' => 'string',
        'coordinates' => 'array',
        'postal_code' => 'string',
        'city' => 'string',
        'country' => 'string',
        'is_active' => 'boolean',
        'established_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class);
    }

    /**
     * Get all plants in this garden through its areas
     */
    public function plants(): Builder
    {
        return Plant::whereHas('areas', function (Builder $query): void {
            $query->where('garden_id', $this->id);
        });
    }

    /**
     * Get the total quantity of plants in this garden.
     */
    public function getTotalPlantQuantity(): int
    {
        return (int) $this->areas()
            ->join('area_plant', 'areas.id', '=', 'area_plant.area_id')
            ->sum('area_plant.quantity');
    }

    /**
     * Alternative method to get total plant quantity.
     */
    public function plantQuantityTotal(): int
    {
        return (int) $this->areas()
            ->join('area_plant', 'areas.id', '=', 'area_plant.area_id')
            ->sum('area_plant.quantity');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeByType(Builder $query, GardenTypeEnum $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeByLocation(Builder $query, string $location): Builder
    {
        return $query->where(function (Builder $q) use ($location): void {
            $q->where('location', 'like', "%{$location}%")
                ->orWhere('city', 'like', "%{$location}%")
                ->orWhere('postal_code', 'like', "%{$location}%");
        });
    }

    public function getFormattedSizeAttribute(): string
    {
        if ($this->size_sqm === null) {
            return 'Größe nicht angegeben';
        }

        return number_format((float) $this->size_sqm, 2, ',', '.').' m²';
    }

    public function getAgeInYearsAttribute(): ?int
    {
        if ($this->established_at === null) {
            return null;
        }

        return (int) $this->established_at->diffInYears(Carbon::now());
    }

    public function getFormattedAgeAttribute(): string
    {
        return $this->established_at
            ? $this->established_at->format('d.m.Y')
            : 'Anlegedatum nicht angegeben';
    }

    public function getAgeDisplayAttribute(): string
    {
        $ageInYears = $this->age_in_years;

        if ($ageInYears === null) {
            return 'Alter unbekannt';
        }

        if ($ageInYears === 1) {
            return '1 Jahr alt';
        }

        return $ageInYears.' Jahre alt';
    }

    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at
            ? $this->created_at->format('d.m.Y H:i')
            : 'Anlegedatum nicht angegeben';
    }

    public function getFormattedUpdatedAtAttribute(): string
    {
        return $this->updated_at
            ? $this->updated_at->format('d.m.Y H:i')
            : 'Anlegedatum nicht angegeben';
    }

    public function hasCoordinates(): bool
    {
        return $this->coordinates !== null
            && isset($this->coordinates['lat'])
            && isset($this->coordinates['lng']);
    }

    public function getLatitude(): ?float
    {
        return $this->hasCoordinates() ? (float) $this->coordinates['lat'] : null;
    }

    public function getLongitude(): ?float
    {
        return $this->hasCoordinates() ? (float) $this->coordinates['lng'] : null;
    }

    public function setCoordinates(float $latitude, float $longitude): void
    {
        $this->coordinates = [
            'lat' => $latitude,
            'lng' => $longitude,
        ];
    }

    public function getFullLocationAttribute(): string
    {
        $parts = array_filter([
            $this->attributes['location'],
            $this->postal_code,
            $this->city,
        ]);

        return count($parts) === 0 ? 'Standort nicht angegeben' : implode(', ', $parts);
    }

    public function getLocationAttribute(): ?string
    {
        return $this->attributes['location'];
    }

    public function getDetails(): array
    {
        return [
            'type' => [
                'label' => 'Typ',
                'value' => $this->type->getLabel(),
            ],
            'size' => [
                'label' => 'Größe',
                'value' => $this->formatted_size,
            ],
            'location' => [
                'label' => 'Standort',
                'value' => $this->location ?? 'Standort nicht angegeben',
            ],
            'address' => [
                'label' => 'Adresse',
                'value' => $this->full_location,
            ],
            'established_at' => [
                'label' => 'Anlegedatum',
                'value' => $this->established_at ? $this->established_at->format('d.m.Y') : 'Nicht angegeben',
            ],
            'age' => [
                'label' => 'Alter',
                'value' => $this->age_display,
            ],
            'status' => [
                'label' => 'Status',
                'value' => $this->is_active ? 'active' : 'inactive',
            ],
            'created_at' => [
                'label' => 'Angelegt am',
                'value' => $this->formatted_created_at,
            ],
            'updated_at' => [
                'label' => 'Aktualisiert am',
                'value' => $this->formatted_updated_at,
            ],
        ];
    }

    protected static function booted(): void
    {
        self::restored(function (Garden $garden): void {
            $garden->areas()->withTrashed()->restore();
        });
    }
}
