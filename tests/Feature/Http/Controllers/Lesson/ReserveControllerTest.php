<?php

namespace Tests\Feature\Http\Controllers\Lesson;

use App\Models\Lesson;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ReserveControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testInvoke_正常系()
    {
        $lesson = Lesson::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user->first());

        $response = $this->post("/lessons/{$lesson->id}/reserve");

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect("/lessons/{$lesson->id}");

        $this->assertDatabaseHas('reservations', [
            'lesson_id' => $lesson->id,
            'user_id' => $user->id,
        ]);
    }

    public function testInvoke_異常系()
    {
        $lesson = Lesson::factory()->create(['capacity' => 1]);
        $anotherUser = User::factory()->create();
        $lesson->reservations()->save(Reservation::factory()->make(['user_id' => $anotherUser->id]));

        $user = User::factory()->create();
        $this->actingAs($user->first());

        $response = $this->from("/lessons/{$lesson->id}")
            ->post("/lessons/{$lesson->id}/reserve");

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect("/lessons/{$lesson->id}");
        $response->assertSessionHasErrors();

        $error = session('errors')->first();
        $this->assertStringContainsString('予約できません。', $error);

        $this->assertDatabaseMissing('reservations', [
            'lesson_id' => $lesson->id,
            'user_id' => $user->id,
        ]);
    }
}
