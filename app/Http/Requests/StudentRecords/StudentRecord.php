<?php

namespace App\Http\Requests\StudentRecords;

use Illuminate\Foundation\Http\FormRequest;

class StudentRecord extends FormRequest
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
            'user_id' => 'required|integer|exists:users,id',
            'intakeID' => 'required|integer|exists:ac_program_intakes,id',
            'level_id' => 'required|integer|exists:ac_course_levels,id',
            'typeID' => 'required|integer|exists:ac_periodTypes,id'
        ];
    }
}
