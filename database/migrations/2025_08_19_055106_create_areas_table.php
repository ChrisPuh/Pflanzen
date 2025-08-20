<?php

declare(strict_types=1);

use App\Enums\Area\AreaTypeEnum;
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
        Schema::create('areas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('garden_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', AreaTypeEnum::values());
            $table->text('description')->nullable();
            $table->decimal('size_sqm', 8, 2)->nullable();
            $table->json('coordinates')->nullable(); // For positioning within garden
            $table->json('dimensions')->nullable(); // Length, width, height if applicable
            $table->string('color', 7)->nullable(); // Hex color for visualization
            $table->json('metadata')->nullable(); // Additional flexible data
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['garden_id', 'is_active']);
            $table->index(['type']);
            $table->index(['deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
