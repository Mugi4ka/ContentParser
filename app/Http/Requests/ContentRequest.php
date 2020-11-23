<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContentRequest extends FormRequest
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
            'content' => 'required|max:300'
        ];
    }

    public function messages()
    {
        return [
            'content.required' => 'Поле нужно заполнить',
            'sitemap.*\|*' => 'Введите слова согласно шаблону',
        ];
    }
}
