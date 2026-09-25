<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_dashboard_lists_tasks_and_filters_by_status(): void
    {
        Task::factory()->create(['title' => 'Check the oxygen systems', 'status' => 'pending']);
        Task::factory()->create(['title' => 'Chart the moon route', 'status' => 'completed']);

        $this->get(route('tasks.index'))
            ->assertSee('Check the oxygen systems')
            ->assertSee('Chart the moon route');

        $this->get(route('tasks.index', ['status' => 'pending']))
            ->assertSee('Check the oxygen systems')
            ->assertDontSee('Chart the moon route');
    }

    public function test_dashboard_assets_use_the_forwarded_https_origin(): void
    {
        $this->withHeaders([
            'X-Forwarded-Host' => 'mission-control.example.test',
            'X-Forwarded-Proto' => 'https',
        ])->get(route('tasks.index'))
            ->assertSee('href="https://mission-control.example.test/build/assets/app-', false)
            ->assertSee('https://mission-control.example.test/images/earth-orbit.jpg');
    }

    public function test_new_task_is_created_as_pending(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title' => 'Prepare the launch checklist',
            'description' => 'Confirm the cabin is ready.',
            'due_date' => '2026-10-04',
            'status' => 'completed',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'title' => 'Prepare the launch checklist',
            'description' => 'Confirm the cabin is ready.',
            'status' => 'pending',
        ]);
        $this->assertSame('2026-10-04', Task::query()->firstOrFail()->due_date->toDateString());
    }

    public function test_task_title_is_required_to_create_a_mission(): void
    {
        $this->from(route('tasks.create'))
            ->post(route('tasks.store'), ['title' => ''])
            ->assertRedirect(route('tasks.create'))
            ->assertSessionHasErrors(['title']);

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_task_can_be_updated_from_its_edit_form(): void
    {
        $task = Task::factory()->create(['title' => 'Inspect the rover']);

        $this->get(route('tasks.edit', $task))->assertSee('Adjust your course.');

        $this->put(route('tasks.update', $task), [
            'title' => 'Inspect the lunar rover',
            'description' => 'Check wheels and comms.',
            'due_date' => '2026-10-08',
            'status' => 'completed',
        ])->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Inspect the lunar rover',
            'description' => 'Check wheels and comms.',
            'status' => 'completed',
        ]);
        $this->assertSame('2026-10-08', $task->fresh()->due_date->toDateString());
    }

    public function test_task_status_can_be_changed_to_completed(): void
    {
        $task = Task::factory()->create(['status' => 'pending']);

        $this->patch(route('tasks.status', $task), ['status' => 'completed'])
            ->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'completed']);
    }

    public function test_invalid_task_status_is_rejected_without_changing_the_task(): void
    {
        $task = Task::factory()->create(['status' => 'pending']);

        $this->patch(route('tasks.status', $task), ['status' => 'launched'])
            ->assertSessionHasErrors(['status']);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'pending']);
    }

    public function test_task_can_be_deleted(): void
    {
        $task = Task::factory()->create();

        $this->delete(route('tasks.destroy', $task))->assertRedirect(route('tasks.index'));

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_dashboard_escapes_task_content(): void
    {
        Task::factory()->create([
            'title' => '<script>alert("xss")</script>',
            'description' => '<img src=x onerror=alert(1)>',
        ]);

        $this->get(route('tasks.index'))
            ->assertSee('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert("xss")</script>', false)
            ->assertDontSee('<img src=x onerror=alert(1)>', false);
    }
}
