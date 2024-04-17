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
            'phone' => 'required|unique:clients_models|min:9|max:50',
            'name' => 'required|string',
            'email' => 'email|string|nullable',
            'address' => 'string|max:250|nullable',
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
            'phone.max' => 'Телефон должен быть меньше 50 цифр',
            'phone.unique' => 'Клиент с таким номером телефона уже существует',
        ];
    }
}
