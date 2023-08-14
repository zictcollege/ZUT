<?php

namespace App\Http\Requests;

use App\Helpers\Qs;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $store =  [
            'first_name' => 'required|string|min:3|max:150',
            'middle_name' => 'sometimes|nullable|string|min:3|max:150',
            'last_name' => 'required|string|min:3|max:150',
            'password' => 'nullable|string|min:3|max:50',
            'gender' => 'required|string',
            'phone' => 'sometimes|nullable|string|min:6|max:20',
            'email' => 'sometimes|nullable|email|max:100|unique:users',
            'image' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:2048',
            //'address' => 'required|string|min:6|max:120',
            'passport'=> 'sometimes|nullable|string|min:6|max:20',
            'nrc' => 'required|regex:/^[0-9]{6}\/[0-9]{2}\/[0-9]{1}/',
            'dob' => 'required',
            'marital_status'=>'required',
            'street_main'=>'required',
            'province_state'=>'required',
            'town_city'=>'required',
            'telephone'=>'sometimes|nullable',
            'mobile'=>'required',
            'nationality'=>'required',
            //'country_of_residence'=>'required'
        ];
        $update =  [
            'first_name' => 'required|string|min:3|max:150',
            'middle_name' => 'sometimes|nullable|string|min:3|max:150',
            'last_name' => 'required|string|min:3|max:150',
            'password' => 'nullable|string|min:3|max:50',
            'gender' => 'required|string',
            'phone' => 'sometimes|nullable|string|min:6|max:20',
            'email' => 'sometimes|nullable|email|max:100|unique:users',
            'image' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:2048',
            //'address' => 'required|string|min:6|max:120',
            'passport'=> 'sometimes|nullable|string|min:6|max:20',
            'nrc' => 'required|regex:/^[0-9]{6}\/[0-9]{2}\/[0-9]{1}/',
            'dob' => 'required',
            'marital_status'=>'required',
            'street_main'=>'required',
            'province_state'=>'required',
            'town_city'=>'required',
            'telephone'=>'sometimes|nullable',
            'mobile'=>'required',
            'nationality'=>'required',
            //'country_of_residence'=>'required'
        ];
        return ($this->method() === 'POST') ? $store : $update;
    }

    public function attributes()
    {
        return  [
            'nal_id' => 'Nationality',
            'state_id' => 'State',
            'lga_id' => 'LGA',
            'user_type' => 'User',
            'phone2' => 'Telephone',
        ];
    }

    protected function getValidatorInstance()
    {
        if($this->method() === 'POST'){
            $input = $this->all();

            //$input['user_type'] = Qs::decodeHash($input['user_type']);

            $this->getInputSource()->replace($input);

        }

        if($this->method() === 'PUT'){
            $this->user = Qs::decodeHash($this->user);
        }

        return parent::getValidatorInstance();

    }
}
