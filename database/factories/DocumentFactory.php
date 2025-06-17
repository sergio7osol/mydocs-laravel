<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get or create a user
        $user = User::inRandomOrder()->first();
        if (!$user) {
            $user = User::factory()->create();
        }

        // Get or create a category
        $category = Category::inRandomOrder()->first();
        if (!$category) {
            $category = Category::factory()->create();
        }

        // Random file extension and MIME type
        $fileTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'txt' => 'text/plain',
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
        ];
        
        $extension = fake()->randomElement(array_keys($fileTypes));
        $mimeType = $fileTypes[$extension];
        $filename = fake()->word() . '.' . $extension;
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
        
        return [
            'title' => fake()->sentence(3),
            'filename' => $filename,
            'file_path' => ($isImage ? 'images/' : 'documents/') . $filename,
            'file_size' => fake()->numberBetween(1000, 10000000),
            'file_type' => $mimeType,
            'user_id' => $user->id, // ✓ USE THE ACTUAL USER ID
            'created_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'description' => fake()->paragraph(2),
            'category_id' => $category->id,
        ];
    }
    
    /**
     * Generate a PDF document
     */
    public function pdf()
    {
        return $this->state(function (array $attributes) {
            $filename = fake()->word() . '.pdf';
            return [
                'filename' => $filename,
                'file_path' => 'documents/' . $filename,
                'file_type' => 'application/pdf',
            ];
        });
    }
    
    /**
     * Generate a Word document
     */
    public function word()
    {
        return $this->state(function (array $attributes) {
            $filename = fake()->word() . '.docx';
            return [
                'filename' => $filename,
                'file_path' => 'documents/' . $filename,
                'file_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ];
        });
    }
    
    /**
     * Generate an image document
     */
    public function image()
    {
        return $this->state(function (array $attributes) {
            $extensions = ['jpg', 'png', 'gif'];
            $extension = fake()->randomElement($extensions);
            $filename = fake()->word() . '.' . $extension;
            return [
                'filename' => $filename,
                'file_path' => 'images/' . $filename,
                'file_type' => 'image/' . ($extension === 'jpg' ? 'jpeg' : $extension),
            ];
        });
    }
}
