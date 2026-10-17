<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Program;
use App\Models\ProgramUserAssignment;
use App\Models\RoutineActivity;
use App\Models\RoutineActivityLog;
use App\Models\Logbook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Tests\TestCase;

class RoutineActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_volunteer_can_view_routine_activities(): void
    {
        $user = User::factory()->create(['role' => 'volunteer', 'status' => 'active']);
        
        $program = Program::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Mangrove Planting 2026',
            'slug' => 'mangrove-planting-2026',
            'description' => 'Mangrove Planting Program',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
        ]);

        ProgramUserAssignment::create([
            'user_id' => $user->id,
            'program_id' => $program->id,
        ]);

        $routine = RoutineActivity::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'program_id' => $program->id,
            'name' => 'Briefing Pagi',
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'is_selfie_required' => false,
            'is_notes_required' => false,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/volunteer/workspace/routines');

        $response->assertOk();
        $response->assertSee('Briefing Pagi');
    }

    public function test_volunteer_can_submit_routine_activity_log(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'volunteer', 'status' => 'active']);
        
        $program = Program::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Mangrove Planting 2026',
            'slug' => 'mangrove-planting-2026',
            'description' => 'Mangrove Planting Program',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
        ]);

        ProgramUserAssignment::create([
            'user_id' => $user->id,
            'program_id' => $program->id,
        ]);

        $routine = RoutineActivity::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'program_id' => $program->id,
            'name' => 'Briefing Pagi',
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'is_selfie_required' => true,
            'is_notes_required' => true,
        ]);

        $photo = UploadedFile::fake()->image('selfie.jpg');

        $response = $this
            ->actingAs($user)
            ->post("/volunteer/workspace/routines/submit/{$routine->id}", [
                'photo' => $photo,
                'notes' => 'Hadir tepat waktu dan mendengarkan briefing.',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert database records were created
        $this->assertDatabaseHas('routine_activity_logs', [
            'user_id' => $user->id,
            'routine_activity_id' => $routine->id,
            'notes' => 'Hadir tepat waktu dan mendengarkan briefing.',
        ]);

        $this->assertDatabaseHas('logbooks', [
            'user_id' => $user->id,
            'program_id' => $program->id,
            'title' => '[Kegiatan Rutin] Briefing Pagi',
            'content' => 'Hadir tepat waktu dan mendengarkan briefing.',
            'status' => 'pending',
        ]);
    }
}
