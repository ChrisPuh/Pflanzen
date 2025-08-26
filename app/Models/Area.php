<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Area\AreaTypeEnum;
use App\Models\Area\Traits\HasAreaScopes;
use Carbon\Carbon;
use Database\Factories\AreaFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $garden_id
 * @property string $name
 * @property AreaTypeEnum $type
 * @property string|null $description
 * @property float|null $size_sqm
 * @property array|null $coordinates
 * @property array|null $dimensions
 * @property string|null $color
 * @property array|null $metadata
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Garden $garden
 * @property-read Plant[] $plants
 *
 * @method static Builder|self forGarden(?int $gardenId)
 * @method static Builder|self byType(?AreaTypeEnum $type)
 * @method static Builder|self byCategory(?string $category)
 * @method static Builder|self search(?string $term)
 */
final class Area extends Model
{
    /** @use HasFactory<AreaFactory> */
    use HasAreaScopes, HasFactory, SoftDeletes;

    protected $table = 'areas';

    protected $fillable = [
        'garden_id',
        'name',
        'type',
        'description',
        'size_sqm',
        'coordinates',
        'dimensions',
        'color',
        'metadata',
        'is_active',
    ];

    protected $casts = [
        'name' => 'string',
        'type' => AreaTypeEnum::class,
        'description' => 'string',
        'size_sqm' => 'float',
        'coordinates' => 'array',
        'dimensions' => 'array',
        'color' => 'string',
        'metadata' => 'array',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function garden(): BelongsTo
    {
        return $this->belongsTo(Garden::class, 'garden_id', 'id', 'gardens');
    }

    public function plants(): BelongsToMany
    {
        return $this->belongsToMany(Plant::class)
            ->withPivot(['quantity', 'planted_at', 'notes'])
            ->withTimestamps();
    }

    public function getFormattedSizeAttribute(): string
    {
        if ($this->size_sqm === null) {
            return 'Größe nicht angegeben';
        }

        return number_format((float)$this->size_sqm, 2, ',', '.') . ' m²';
    }

    public function hasCoordinates(): bool
    {
        return $this->coordinates !== null
            && isset($this->coordinates['x'])
            && isset($this->coordinates['y']);
    }

    public function getXCoordinate(): ?float
    {
        return $this->hasCoordinates() ? (float)$this->coordinates['x'] : null;
    }

    public function getYCoordinate(): ?float
    {
        return $this->hasCoordinates() ? (float)$this->coordinates['y'] : null;
    }

    public function setCoordinates(float $x, float $y): void
    {
        $this->coordinates = [
            'x' => $x,
            'y' => $y,
        ];
    }

    public function hasDimensions(): bool
    {
        return $this->dimensions !== null
            && (isset($this->dimensions['length']) || isset($this->dimensions['width']));
    }

    public function getLength(): ?float
    {
        return $this->hasDimensions() && isset($this->dimensions['length'])
            ? (float)$this->dimensions['length']
            : null;
    }

    public function getWidth(): ?float
    {
        return $this->hasDimensions() && isset($this->dimensions['width'])
            ? (float)$this->dimensions['width']
            : null;
    }

    public function getHeight(): ?float
    {
        return $this->hasDimensions() && isset($this->dimensions['height'])
            ? (float)$this->dimensions['height']
            : null;
    }

    public function setDimensions(?float $length = null, ?float $width = null, ?float $height = null): void
    {
        $dimensions = [];

        if ($length !== null) {
            $dimensions['length'] = $length;
        }

        if ($width !== null) {
            $dimensions['width'] = $width;
        }

        if ($height !== null) {
            $dimensions['height'] = $height;
        }

        $this->dimensions = $dimensions === [] ? null : $dimensions;
    }

    public function getDisplayColor(): string
    {
        if ($this->color) {
            return $this->color;
        }

        // Default colors by type category
        return match ($this->type->category()) {
            'Pflanzbereich' => '#22c55e', // Green
            'Grünfläche' => '#65a30d', // Lime green
            'Aufenthaltsbereich' => '#f59e0b', // Amber
            'Gebäude' => '#6b7280', // Gray
            'Wasserelement' => '#3b82f6', // Blue
            'Funktionsbereich' => '#8b5cf6', // Violet
            'Gehölz' => '#059669', // Emerald
            'Freizeit' => '#ec4899', // Pink
            default => '#6b7280', // Gray
        };
    }

    public function isPlantingArea(): bool
    {
        return $this->type->isPlantingArea();
    }

    public function isWaterFeature(): bool
    {
        return $this->type->isWaterFeature();
    }

    public function isBuilding(): bool
    {
        return $this->type->isBuilding();
    }

    public function getMetadataValue(string $key, mixed $default = null): mixed
    {
        return $this->metadata[$key] ?? $default;
    }

    public function setMetadataValue(string $key, mixed $value): void
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->metadata = $metadata;
    }
}
