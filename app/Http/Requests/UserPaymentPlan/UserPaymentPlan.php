<?php

namespace App\Http\Requests\UserPaymentPlan;

use Illuminate\Foundation\Http\FormRequest;

class UserPaymentPlan extends FormRequest
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
            'userID' => 'required|integer|exists:users,id',
            'paymentPlanID' => 'required|integer|exists:fn_payment_plans,id',
        ];
    }
}
