<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PlantCategoryEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class PlantCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'description',
    ];

    public function plants(): BelongsToMany
    {
        return $this->belongsToMany(Plant::class, 'category_plant');
    }

    protected function casts(): array
    {
        return [
            'name' => PlantCategoryEnum::class,
        ];
    }
}
