<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Models\Lesson;
use App\Models\VacancyLevel;

class LessonController extends Controller
{
    public function show(Lesson $lesson)
    {
        return view('lesson.show', compact('lesson'));
    }
}
