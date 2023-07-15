<?php

namespace App\Http\Requests\AcademicPeriod;

use App\Helpers\Qs;
use Illuminate\Foundation\Http\FormRequest;

class AcademicPeriodCreate extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'required|string|unique:ac_academicPeriods',
            'registrationDate' => 'required|string|date|unique:ac_academicPeriods',
            'lateRegistrationDate' => 'required|string|date|unique:ac_academicPeriods',
            'acStartDate' => 'required|string|date|unique:ac_academicPeriods',
            'acEndDate' => 'required|string|date',
            'periodID' => 'required|integer',
            'type' => 'required|integer|exists:ac_periodTypes,id',
            'studyModeIDAllowed' => 'required|integer|exists:ac_studyModes,id',
            'registrationThreshold' => 'required|integer',
            'resultsThreshold' => 'required|integer',
            'examSlipThreshold' => 'required|integer',
        ];
    }

}
