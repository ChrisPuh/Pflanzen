<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PlantCategoryEnum;
use App\Enums\PlantTypeEnum;
use App\Models\Category;
use App\Models\Plant;
use App\Models\PlantType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PlantDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only run in non-production environments
        if (app()->environment('production')) {
            $this->command->info('Skipping PlantDataSeeder in production environment.');
            return;
        }

        $this->command->info('Starting PlantDataSeeder...');

        // Get all plant types from enum
        $plantTypes = PlantTypeEnum::cases();

        foreach ($plantTypes as $plantTypeEnum) {
            $this->seedPlantType($plantTypeEnum);
        }

        $this->command->info('PlantDataSeeder completed successfully!');
    }

    /**
     * Seed plants for a specific plant type
     */
    private function seedPlantType(PlantTypeEnum $plantTypeEnum): void
    {
        $typeName = $plantTypeEnum->value;
        $jsonFile = database_path("seeders/data/{$typeName}.json");

        if (!File::exists($jsonFile)) {
            $this->command->warn("JSON file not found: {$jsonFile}");
            return;
        }

        $this->command->info("Seeding plants for type: {$plantTypeEnum->getLabel()}");

        // Get the plant type model
        $plantType = PlantType::where('name', $plantTypeEnum)->first();
        if (!$plantType) {
            $this->command->error("PlantType not found: {$plantTypeEnum->value}");
            return;
        }

        // Read and decode JSON file
        $plantsData = json_decode(File::get($jsonFile), true);

        if (!is_array($plantsData)) {
            $this->command->error("Invalid JSON format in file: {$jsonFile}");
            return;
        }

        foreach ($plantsData as $plantData) {
            $this->createPlant($plantData, $plantType);
        }
    }

    /**
     * Create a plant with its categories
     */
    private function createPlant(array $plantData, PlantType $plantType): void
    {
        // Validate required fields
        if (!isset($plantData['name'])) {
            $this->command->warn('Plant data missing name field, skipping...');
            return;
        }

        // Check if plant already exists
        $existingPlant = Plant::where('name', $plantData['name'])
            ->where('plant_type_id', $plantType->id)
            ->first();

        if ($existingPlant) {
            $this->command->line("Plant already exists: {$plantData['name']}");
            return;
        }

        // Create the plant
        $plant = Plant::create([
            'name' => $plantData['name'],
            'latin_name' => $plantData['latin_name'] ?? null,
            'description' => $plantData['description'] ?? null,
            'plant_type_id' => $plantType->id,
        ]);

        $this->command->line("Created plant: {$plant->name}");

        // Attach categories if provided
        if (isset($plantData['categories']) && is_array($plantData['categories'])) {
            $this->attachCategories($plant, $plantData['categories']);
        }
    }

    /**
     * Attach categories to a plant
     */
    private function attachCategories(Plant $plant, array $categoryNames): void
    {
        $categoryIds = [];

        foreach ($categoryNames as $categoryName) {
            try {
                // Convert string to enum
                $categoryEnum = PlantCategoryEnum::from($categoryName);
                
                // Find the category model
                $category = Category::where('name', $categoryEnum)->first();
                
                if ($category) {
                    $categoryIds[] = $category->id;
                } else {
                    $this->command->warn("Category not found: {$categoryName}");
                }
            } catch (\ValueError $e) {
                $this->command->warn("Invalid category: {$categoryName}");
            }
        }

        if (!empty($categoryIds)) {
            $plant->categories()->attach($categoryIds);
            $this->command->line("  → Attached " . count($categoryIds) . " categories to {$plant->name}");
        }
    }
}
