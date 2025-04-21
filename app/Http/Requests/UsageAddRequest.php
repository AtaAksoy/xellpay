<?php

namespace App\Http\Requests;

use App\Enums\FeatureType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UsageAddRequest extends FormRequest
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
            'subscriber_no' => 'required|string',
            'month' => 'required|integer|between:1,12',
            'usage_type' => [Rule::enum(FeatureType::class)],
            'usage_amount' => 'required|integer'
        ];
    }
}
