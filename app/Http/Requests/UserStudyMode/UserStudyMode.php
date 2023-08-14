<?php

namespace App\Http\Requests\UserStudyMode;

use Illuminate\Foundation\Http\FormRequest;

class UserStudyMode extends FormRequest
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
            'studyModeID' => 'required|integer|exists:ac_studyModes,id',
        ];
    }
}
