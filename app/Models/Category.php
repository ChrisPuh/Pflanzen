<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PlantCategoryEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function plants(): BelongsToMany
    {
        return $this->belongsToMany(Plant::class)->withTimestamps();
    }

    protected function casts(): array
    {
        return [
            'name' => PlantCategoryEnum::class,
        ];
    }
}
