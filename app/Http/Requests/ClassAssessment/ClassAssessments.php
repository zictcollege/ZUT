<?php

namespace App\Http\Requests\ClassAssessment;

use Illuminate\Foundation\Http\FormRequest;

class ClassAssessments extends FormRequest
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
            'assesmentID' => 'required|integer|exists:ac_assessmentTypes,id',
            'classID' => 'required|integer|exists:ac_classes,id',
            'total' => 'required|integer',
            'end_date' =>'required|string|date'
        ];
    }
}
