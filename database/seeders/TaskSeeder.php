<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        Task::factory()->create([
            'title' => 'Review the lunar landing checklist',
            'description' => 'Confirm the systems, supplies, and crew notes before descent.',
            'due_date' => today()->addDay(),
            'status' => 'pending',
        ]);

        Task::factory()->create([
            'title' => 'Chart a route through the asteroid belt',
            'description' => 'Mark the clear corridor and share the flight plan.',
            'due_date' => today()->addDays(3),
            'status' => 'pending',
        ]);

        Task::factory()->create([
            'title' => 'Log the last supply drop',
            'description' => 'Inventory received and stored on the station.',
            'due_date' => today()->subDay(),
            'status' => 'completed',
        ]);
    }
}
