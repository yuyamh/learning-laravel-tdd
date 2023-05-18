<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // 明日から10日後までの間でランダムな日時を生成する
        $startAt = $this->faker->dateTimeBetween('+1 days', '+10 days');
        $startAt->setTime(10, 0, 0);
        $endAt = clone $startAt;
        $endAt->setTime(11, 0, 0);

        return [
            'name' => $this->faker->name,
            'coach_name' => $this->faker->name,
            'capacity' => $this->faker->randomNumber(2),
            'start_at' => $startAt->format('Y-m-d H:i:s'),
            'end_at' => $endAt->format('Y-m-d H:i:s'),
        ];
    }

    public function past()
    {
        return $this->state(function (array $attributes)
        {
            // 10日前から昨日までの間でランダムな日時を生成する
            $startAt = $this->faker->dateTimeBetween('-10 days', '-1 days');
            $startAt->setTime(10, 0, 0);
            $endAt = clone $startAt;
            $endAt->setTime(11, 0, 0);

            return [
                'start_at' => $startAt->format('Y-m-d H:i:s'),
                'end_at' => $endAt->format('Y-m-d H:i:s'),
            ];
        });
    }
}
