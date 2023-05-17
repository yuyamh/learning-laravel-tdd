<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Lesson;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LessonControllerTest extends TestCase
{

    use RefreshDatabase;

    /**
     * @param int $capacity
     * @param int $reservationCount
     * @param string $expectedVacancyLevelMark
     * @param string $button
     * @dataProvider dataShow
     */
    public function testShow(int $capacity, int $reservationCount, string $expectedVacancyLevelMark, string $button)
    {
        $lesson = Lesson::factory()->create(['name' => '楽しいヨガレッスン', 'capacity' => $capacity]);
        for ($i = 0; $i < $reservationCount; $i++)
        {
            $user = User::factory()->create();
            Reservation::factory()->create(['lesson_id' => $lesson->id, 'user_id' => $user->id]);
        }

        $user = User::factory()->create();
        $this->actingAs($user->first());

        $response = $this->get("/lessons/{$lesson->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee($lesson->name);
        $response->assertSee("空き状況: {$expectedVacancyLevelMark}");

        $response->assertSee($button, false);
    }

    public function dataShow()
    {
        $button = '<button class="btn btn-primary">このレッスンを予約する</button>';
        $span = '<span class="btn btn-primary disabled">予約できません</span>';

        return [
            '空き十分' => [
                'capacity' => 6,
                'reservationCount' => 1,
                'expectedVacancyLevelMark' => '◎',
                'button' => $button,
            ],
            '空きわずか' => [
                'capacity' => 6,
                'reservationCount' => 2,
                'expectedVacancyLevelMark' => '△',
                'button' => $button,
            ],
            '空きなし' => [
                'capacity' => 1,
                'reservationCount' => 1,
                'expectedVacancyLevelMark' => '×',
                'button' => $span,
            ],
        ];
    }
}
