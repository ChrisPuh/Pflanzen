<?php

declare(strict_types=1);

use App\Enums\PlantCategory as PlantCategoryEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plant_categories', function (Blueprint $table): void {
            $table->id();
            $table->enum('name', PlantCategoryEnum::values())->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed all plant categories
        foreach (PlantCategoryEnum::cases() as $category) {
            DB::table('plant_categories')->insert([
                'name' => $category->value,
                'description' => 'Default description for '.$category->getLabel(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plant_categories');
    }
};
