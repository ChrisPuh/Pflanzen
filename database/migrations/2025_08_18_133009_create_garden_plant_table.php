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
        Schema::create('garden_plant', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('garden_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plant_id')->constrained()->cascadeOnDelete();
            $table->date('planted_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['garden_id', 'plant_id']);
            $table->index(['garden_id']);
            $table->index(['plant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garden_plant');
    }
};
