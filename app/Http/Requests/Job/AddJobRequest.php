<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class AddJobRequest extends FormRequest
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
        return [
            'name' => 'required|max:255',
            'customer' => 'required|exists:customers,id',
            'type' => 'required|exists:types,id',
            'method' => 'required|exists:methods,id',
            'start_date' => 'required|date:Y-m-d',
            'finish_date' => 'required|date:Y-m-d',
            'price_yen' => 'required|numeric|min:1',
            'note' => 'nullable|max:60000',
        ];
    }
}
