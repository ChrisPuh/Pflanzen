<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PlantType as PlantTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class PlantType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function plants(): HasMany
    {
        return $this->hasMany(Plant::class);
    }

    protected function casts(): array
    {
        return [
            'name' => PlantTypeEnum::class,
        ];
    }
}
