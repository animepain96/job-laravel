<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
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
        $validate = [
            'field' => 'in:name,role,active',
        ];
        if($this->get('field') === 'name') {
            $validate['value'] = 'required|max:255';
        } elseif($this->get('field') === 'role') {
            $validate['value'] = 'required|in:admin,user';
        } else {
            $validate['value'] = 'required|boolean';
        }
        return $validate;
    }
}
