<?php

namespace App\Http\Requests\ProgramCourses;

use Illuminate\Foundation\Http\FormRequest;

class ProgramCourse extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'level_id' => 'required|integer|exists:ac_course_levels,id',
            'courseID' => 'required|array',
            'courseID.*' => 'exists:ac_courses,id',
            'programID' => 'required|integer|exists:ac_programs,id',
        ];
    }
}
