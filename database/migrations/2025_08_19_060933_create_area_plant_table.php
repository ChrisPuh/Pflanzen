<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('area_plant', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('area_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plant_id')->constrained()->cascadeOnDelete();
            $table->timestamp('planted_at')->nullable();
            $table->text('notes')->nullable();
            $table->integer('quantity')->default(1);

            $table->timestamps();

            $table->unique(['area_id', 'plant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_plant');
    }
};
