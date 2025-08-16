<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Plant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'latin_name',
        'description',
        'plant_type_id',
    ];

    public function plantType(): BelongsTo
    {
        return $this->belongsTo(PlantType::class);
    }

    public function plantCategories(): BelongsToMany
    {
        return $this->belongsToMany(PlantCategory::class, 'category_plant');
    }
}
