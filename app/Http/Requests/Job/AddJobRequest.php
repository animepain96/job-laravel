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
            'Name' => 'required|max:255',
            'Customer' => 'required|exists:Customer,id',
            'Type' => 'required|exists:JType,id',
            'Method' => 'required|exists:JMethod,id',
            'StartDate' => 'required|date:Y-m-d',
            'FinishDate' => 'required|date:Y-m-d',
            'PriceYen' => 'required|numeric|min:1',
            'Note' => 'nullable|max:10000',
        ];
    }
}
