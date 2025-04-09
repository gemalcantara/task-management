<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->sentence(rand(3, 6)),
            'content' => fake()->paragraph(rand(3, 5)),
            'status' => fake()->randomElement(['to_do', 'in_progress', 'done']),
            'visibility' => fake()->randomElement(['draft', 'published']),
            'image' => null,
            'user_id' => User::factory(),
            'parent_id' => null,
        ];
    }

    /**
     * Indicate that the task is marked as done.
     */
    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'done',
        ]);
    }

    /**
     * Indicate that the task is marked as in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
        ]);
    }

    /**
     * Indicate that the task is marked as to do.
     */
    public function toDo(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'to_do',
        ]);
    }

    /**
     * Indicate that the task is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => 'published',
        ]);
    }
}