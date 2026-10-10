<?php

return [
    'accepted' => 'Необходимо принять :attribute.',
    'confirmed' => 'Поле :attribute не совпадает с подтверждением.',
    'email' => 'Поле :attribute должно быть корректным адресом email.',
    'exists' => 'Выбранное значение :attribute неверно.',
    'image' => 'Поле :attribute должно быть изображением.',
    'integer' => 'Поле :attribute должно быть целым числом.',
    'numeric' => 'Поле :attribute должно быть числом.',
    'required' => 'Поле :attribute обязательно для заполнения.',
    'string' => 'Поле :attribute должно быть строкой.',
    'unique' => 'Такое значение поля :attribute уже используется.',
    'url' => 'Поле :attribute должно быть корректной ссылкой.',
    'date' => 'Поле :attribute должно быть датой.',
    'same' => 'Поля :attribute и :other должны совпадать.',
    'max' => [
        'numeric' => 'Поле :attribute не может быть больше :max.',
        'file' => 'Файл :attribute не может быть больше :max КБ.',
        'string' => 'Поле :attribute не может быть длиннее :max символов.',
        'array' => 'В поле :attribute не может быть больше :max элементов.',
    ],
    'min' => [
        'numeric' => 'Поле :attribute должно быть не меньше :min.',
        'file' => 'Файл :attribute должен быть не меньше :min КБ.',
        'string' => 'Поле :attribute должно содержать не менее :min символов.',
        'array' => 'В поле :attribute должно быть не менее :min элементов.',
    ],
    'custom' => [],
    'attributes' => [
        'name' => 'имя',
        'email' => 'email',
        'password' => 'пароль',
        'password_confirmation' => 'подтверждение пароля',
        'current_password' => 'текущий пароль',
        'title' => 'название',
        'description' => 'описание',
    ],
];
