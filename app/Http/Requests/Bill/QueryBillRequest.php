<?php

namespace App\Http\Requests\Bill;

use Illuminate\Foundation\Http\FormRequest;

class QueryBillRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'subscriber_no' => 'required|integer|exists:users,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer'
        ];
    }
}
