<?php

return [
    'room_id' => [
        'required' => 'Room is required',
        'exists' => 'Selected room does not exist',
    ],
    'date' => [
        'required' => 'Booking date is required',
        'date' => 'Booking date must be a valid date',
    ],
    'from' => [
        'date' => 'The ":attribute" must be a valid date.',
    ],
    'to' => [
        'date' => 'The ":attribute" must be a valid date.',
        'after_or_equal' => 'The ":attribute" must be the same or after the start date.',
    ],
    'page' => [
        'integer' => 'The ":attribute" must be an integer.',
        'min' => 'The ":attribute" must be at least 1.',
    ],
    'per_page' => [
        'integer' => 'The ":attribute" must be an integer.',
        'min' => 'The ":attribute" must be at least 1.',
        'max' => 'The ":attribute" must not be greater than 100.',
    ],
    'email' => [
        'required' => 'The ":attribute" field is required.',
        'email' => 'The ":attribute" must be a valid email address.',
    ],
    'password' => [
        'required' => 'The ":attribute" field is required.',
        'string' => 'The ":attribute" must be a string.',
    ],
];
