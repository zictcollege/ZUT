<?php

namespace App\Http\Requests\Classes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class Classes extends FormRequest
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
                'instructorID' => 'required|integer',
                'courseID' => 'required|integer',
                'academicPeriodID' => 'required|integer',
        ];
    }
}
