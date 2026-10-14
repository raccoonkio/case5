<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:120'],
            'location' => ['required', 'string', 'min:2', 'max:120'],
            'budget' => ['required', 'numeric', 'min:0', 'max:100000000', 'decimal:0,2'],
            'body' => ['required', 'string', 'min:10', 'max:15000'],
            'places' => ['required', 'string', 'max:2000', 'regex:/\S/u'],
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:max_width=10000,max_height=10000'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Заполните поле «:attribute».',
            'min' => 'Поле «:attribute» слишком короткое или меньше допустимого значения.',
            'max' => 'Поле «:attribute» превышает допустимый размер.',
            'photo.image' => 'Выберите изображение JPG, PNG или WebP.',
            'photo.mimes' => 'Разрешены фотографии JPG, PNG и WebP.',
            'photo.max' => 'Фотография должна быть размером до 4 МБ.',
            'photo.uploaded' => 'Не удалось загрузить фотографию. Выберите файл до 4 МБ.',
            'places.regex' => 'Добавьте хотя бы одно место для посещения.',
            'budget.decimal' => 'Укажите стоимость с точностью до копеек.',
            'numeric' => 'Введите число в поле «:attribute».',
            'string' => 'Поле «:attribute» должно содержать текст.',
            'photo.dimensions' => 'Размер фотографии не должен превышать 10000 × 10000 пикселей.',
        ];
    }

    public function attributes(): array
    {
        return ['title' => 'Название', 'location' => 'Направление', 'budget' => 'Бюджет', 'body' => 'Ваша история', 'places' => 'Места для посещения', 'photo' => 'Фотография'];
    }
}
