<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     * By default, creates root categories (no parent)
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Realistic category names for document management
        $categoryNames = [
            'Documents', 'Images', 'Videos', 'Audio', 'Archives',
            'Reports', 'Presentations', 'Spreadsheets', 'PDFs',
            'Contracts', 'Invoices', 'Projects', 'Personal',
            'Work', 'Education', 'Finance', 'Legal', 'Marketing'
        ];

        return [
            'name' => fake()->randomElement($categoryNames),
            'parent_id' => null, // Root category by default
            'path' => null, // Root categories have no path
            'level' => 0, // Root level
            'is_active' => fake()->boolean(90), // 90% chance of being active
            'display_order' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Create a child category with proper hierarchy
     */
    public function child(?Category $parent = null): static
    {
        return $this->state(function (array $attributes) use ($parent) {
            if (!$parent) {
                // Get or create a random parent category
                $parent = Category::inRandomOrder()->first();
                if (!$parent) {
                    $parent = Category::factory()->create();
                }
            }
            
            // Calculate hierarchical values
            $level = $parent->level + 1;
            $path = $parent->path ? $parent->path . '/' . $parent->name : $parent->name;
            
            // Child-specific names
            $childNames = [
                'Archived', 'Current', 'Draft', 'Final', 'Pending',
                'Review', 'Approved', 'Rejected', 'Backup', 'Templates',
                'Examples', 'Samples', 'Old', 'New', 'Important'
            ];
            
            return [
                'name' => fake()->randomElement($childNames),
                'parent_id' => $parent->id,
                'path' => $path,
                'level' => $level,
            ];
        });
    }

    /**
     * Create an inactive category
     */
    public function inactive(): static
    {
        return $this->state([
            'is_active' => false,
        ]);
    }

    /**
     * Create category with specific name
     */
    public function named(string $name): static
    {
        return $this->state([
            'name' => $name,
        ]);
    }

    /**
     * Create category with specific display order
     */
    public function ordered(int $order): static
    {
        return $this->state([
            'display_order' => $order,
        ]);
    }
}
