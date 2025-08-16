<?php

declare(strict_types=1);

use App\Enums\PlantTypeEnum;
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
        Schema::create('plant_types', function (Blueprint $table): void {
            $table->id();
            $table->enum('name', PlantTypeEnum::values())->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed all plant types
        foreach (PlantTypeEnum::cases() as $type) {
            DB::table('plant_types')->insert([
                'name' => $type->value,
                'description' => 'Default description for '.$type->getLabel(),
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
        Schema::dropIfExists('plant_types');
    }
};
