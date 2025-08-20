<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update any existing records where quantity is 0 or null to 1
        DB::table('area_plant')
            ->where(function ($query): void {
                $query->where('quantity', '<=', 0)
                    ->orWhereNull('quantity');
            })
            ->update(['quantity' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed - we don't want to set quantities back to 0
    }
};
