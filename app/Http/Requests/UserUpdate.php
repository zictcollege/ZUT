<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdate extends FormRequest
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
            'first_name' => 'required|string|min:6|max:150',
            'middle_name' => 'required|string|min:6|max:150',
            'last_name' => 'required|string|min:6|max:150',
            'password' => 'nullable|string|min:3|max:50',
            'user_type' => 'required',
            'gender' => 'required|string',
            'phone' => 'sometimes|nullable|string|min:6|max:20',
            'email' => 'sometimes|nullable|email|max:100|unique:users',
            'image' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:2048',
            'address' => 'required|string|min:6|max:120',
            'passport'=> 'sometimes|nullable|string|min:6|max:20',
            'nrc' => 'regex:/^[0-9]{6}\/[0-9]{2}\/[0-9]{2}$/',
            'dob' => 'required',
            'marital_status'=>'required',
            'street_main'=>'required',
            'province_state'=>'required',
            'town_city'=>'required',
            'telephone'=>'required',
            'mobile'=>'required',
            'nationality'=>'required',
            'country_of_residence'=>'required'
        ];
    }

    public function attributes()
    {
        return  [
            'street_main'=>'required',
            'province_state'=>'required',
            'town_city'=>'required',
            'telephone'=>'required',
            'mobile'=>'required',
            'nationality'=>'required',
        ];
    }
}
