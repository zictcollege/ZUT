<?php

namespace App\Http\Requests\PeriodFees;

use Illuminate\Foundation\Http\FormRequest;

class PeriodFees extends FormRequest
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
            'academicPeriodID' => 'required|integer|exists:ac_academicPeriods,id',
            'feeID' => 'required|integer|exists:ac_fees,id',
            'amount' => 'required|numeric',
            'feetype' => 'required|integer'
        ];
    }
}
