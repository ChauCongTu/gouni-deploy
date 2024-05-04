<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use Faker\Factory as Faker;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 100; $i++) {
            $subjectId = $faker->boolean(50) ? 1 : null;
            $chapterId = $faker->boolean(50) ? 1 : null;

            Question::create([
                'question' => $faker->sentence,
                'answer_1' => $faker->sentence,
                'answer_2' => $faker->sentence,
                'answer_3' => $faker->sentence,
                'answer_4' => $faker->sentence,
                'answer_correct' => $faker->numberBetween(1, 4),
                'answer_detail' => $faker->paragraph,
                'subject_id' => $subjectId,
                'chapter_id' => $chapterId,
                'level' => $faker->numberBetween(1, 5),
            ]);
        }
    }
}
