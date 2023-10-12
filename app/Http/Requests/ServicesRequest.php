<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServicesRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'price' => 'required|numeric',
            'name' => 'required|string|max:255',
            'execution_time.*' => 'nullable|numeric',
            'description' => 'nullable|string|max:500',
            'url_disk' => 'nullable|url',
        ];
    }

    public function messages()
    {
        return [
            'price' => 'В поле цена допускаются только числа (не используйте запятую)',
            'price.required' => 'Цена обязательна',
            'name.required' => 'Название обязательно'
        ];
    }
}
