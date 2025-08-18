<?php

declare(strict_types=1);

use App\Enums\Garden\GardenTypeEnum;
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
        Schema::create('gardens', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', GardenTypeEnum::values());
            $table->string('location')->nullable();
            $table->decimal('size_sqm', 8, 2)->nullable();
            $table->text('description')->nullable();
            $table->json('coordinates')->nullable(); // Lat/Lng für genaue Position
            $table->string('postal_code', 10)->nullable();
            $table->string('city')->nullable();
            $table->string('country', 2)->default('DE');
            $table->boolean('is_active')->default(true);
            $table->date('established_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index(['type']);
            $table->index(['postal_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gardens');
    }
};
