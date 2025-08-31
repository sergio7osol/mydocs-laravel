<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Label>
 */
class LabelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = [
            'draft', 'in progress', 'in review', 'approved', 'rejected',
            'archived', 'high priority', 'low priority', 'needs attention',
            'completed', 'on hold', 'dangerous', 'confidential', 'urgent'
        ];
        
        return [
            'name' => fake()->unique()->randomElement($statuses),
        ];
    }
}
