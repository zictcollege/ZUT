<?php

namespace App\Http\Requests\Prerequisites;

use Illuminate\Foundation\Http\FormRequest;

class Prerequisite extends FormRequest
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
                'courseID' => 'required|integer|exists:ac_courses,id',
                'prerequisiteID' => 'required|array',
                'prerequisiteID.*' => 'exists:ac_courses,id',
        ];
    }
}
