<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'from.date' => __('validation.from.date'),
            'to.date' => __('validation.to.date'),
            'to.after_or_equal' => __('validation.to.after_or_equal'),
            'page.integer' => __('validation.page.integer'),
            'page.min' => __('validation.page.min'),
            'per_page.integer' => __('validation.per_page.integer'),
            'per_page.min' => __('validation.per_page.min'),
            'per_page.max' => __('validation.per_page.max'),
        ];
    }

    public function attributes(): array
    {
        return [
            'from' => __('attributes.from'),
            'to' => __('attributes.to'),
            'page' => __('attributes.page'),
            'per_page' => __('attributes.per_page'),
        ];
    }
}