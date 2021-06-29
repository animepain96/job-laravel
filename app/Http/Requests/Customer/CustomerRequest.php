<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
            'field' => 'in:name,note',
            'value' => [],
        ];
        switch ($this->get('field')) {
            case 'name':
                array_push($validate['value'], 'required', 'max:255');
                break;
            default:
                array_push($validate['value'], 'max:10000');
        }
        return $validate;
    }
}
