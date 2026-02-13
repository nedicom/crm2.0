<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'phone' => 'required|min:9',
            'name' => 'required|string',
            'email' => 'email|string|nullable',
            'address' => 'string|max:250|nullable',
            'api_access' => 'boolean',
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Имя обязательно',
            'phone.required' => 'Телефон обязателен',
            'phone.min' => 'Телефон должен быть больше 9 цифр',
        ];
    }
}
