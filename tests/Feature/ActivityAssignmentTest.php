<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Program;
use App\Models\ProgramKerja;
use App\Models\Kegiatan;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Tests\TestCase;

class ActivityAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_activity_with_volunteer_assignments(): void
    {
        $admin = User::factory()->create(['role' => 'program-admin', 'status' => 'active']);
        $volunteer1 = User::factory()->create(['role' => 'volunteer', 'status' => 'active']);
        $volunteer2 = User::factory()->create(['role' => 'volunteer', 'status' => 'active']);

        $program = Program::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Mangrove Action Program',
            'slug' => 'mangrove-action-program',
            'description' => 'Mangrove program',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
        ]);

        $workPlan = ProgramKerja::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'program_id' => $program->id,
            'name' => 'WBS 1.1 Penanaman',
            'description' => 'Penanaman bibit',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($admin)
            ->post('/program-admin/activity/store', [
                'work_plan_id' => $workPlan->id,
                'name' => 'Briefing Lapangan',
                'description' => 'Briefing sebelum penanaman',
                'start_date' => Carbon::now()->toDateString(),
                'end_date' => Carbon::now()->addDays(5)->toDateString(),
                'assigned_to' => [$volunteer1->id, $volunteer2->id],
            ]);

        $response->assertRedirect();

        // Verify Kegiatan is in database
        $this->assertDatabaseHas('kegiatan', [
            'name' => 'Briefing Lapangan',
        ]);

        $activity = Kegiatan::where('name', 'Briefing Lapangan')->first();

        // Verify corresponding Task was created with correct assigned volunteers
        $this->assertDatabaseHas('tasks', [
            'activity_id' => $activity->id,
            'title' => 'Briefing Lapangan',
        ]);

        $task = Task::where('activity_id', $activity->id)->first();
        $this->assertEquals([$volunteer1->id, $volunteer2->id], $task->assigned_to);
    }

    public function test_admin_can_update_activity_and_sync_task_assignments(): void
    {
        $admin = User::factory()->create(['role' => 'program-admin', 'status' => 'active']);
        $volunteer1 = User::factory()->create(['role' => 'volunteer', 'status' => 'active']);
        $volunteer2 = User::factory()->create(['role' => 'volunteer', 'status' => 'active']);

        $program = Program::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Mangrove Action Program',
            'slug' => 'mangrove-action-program',
            'description' => 'Mangrove program',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
        ]);

        $workPlan = ProgramKerja::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'program_id' => $program->id,
            'name' => 'WBS 1.1 Penanaman',
            'description' => 'Penanaman bibit',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
            'status' => 'active',
        ]);

        $activity = Kegiatan::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'work_plan_id' => $workPlan->id,
            'name' => 'Briefing Lapangan',
            'description' => 'Briefing sebelum penanaman',
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays(5),
        ]);

        $task = Task::create([
            'activity_id' => $activity->id,
            'title' => $activity->name,
            'description' => $activity->description,
            'due_date' => $activity->end_date,
            'status' => 'active',
            'type' => 'individual',
            'assigned_to' => [$volunteer1->id],
        ]);

        $response = $this
            ->actingAs($admin)
            ->post("/program-admin/activity/update/{$activity->id}", [
                'work_plan_id' => $workPlan->id,
                'name' => 'Briefing Lapangan Terupdate',
                'description' => 'Briefing sebelum penanaman baru',
                'start_date' => Carbon::now()->toDateString(),
                'end_date' => Carbon::now()->addDays(5)->toDateString(),
                'assigned_to' => [$volunteer2->id], // updated assignment
            ]);

        $response->assertRedirect();

        // Verify task updated
        $task->refresh();
        $this->assertEquals('Briefing Lapangan Terupdate', $task->title);
        $this->assertEquals([$volunteer2->id], $task->assigned_to);
    }
}
