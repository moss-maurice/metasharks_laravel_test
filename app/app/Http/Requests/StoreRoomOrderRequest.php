<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_id' => ['required', 'exists:rooms,id'],
            'date' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'room_id.required' => __('validation.room_id.required'),
            'room_id.exists' => __('validation.room_id.exists'),
            'date.required' => __('validation.date.required'),
            'date.date' => __('validation.date.date'),
        ];
    }

    public function attributes(): array
    {
        return [
            'room_id' => __('attributes.room_id'),
            'date' => __('attributes.date'),
        ];
    }
}