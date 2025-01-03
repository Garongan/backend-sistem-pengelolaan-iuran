<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResidentRequest extends FormRequest
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
            'data' => [
                'fullname' => ['required', 'string'],
                'is_permanent_resident' => ['required', 'boolean'],
                'phone_number' => ['required', 'min:10'],
                'is_married' => ['required', 'boolean']
            ],
            'identity_card_image' => ['required', 'image:jpg,png|max:2048'],
        ];
    }
}
