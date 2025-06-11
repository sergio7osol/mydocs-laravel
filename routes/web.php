<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'pageTitle' => 'Documents list',
        'users' => [
            [
                'id' => 1,
                'email' => 'sergey8osokin@gmail.com',
                'firstname' => 'Sergey',
                'lastname' => 'Osokin',
            ],
            [
                'id' => 2,
                'email' => 'galina8treneva@gmail.com',
                'firstname' => 'Galina',
                'lastname' => 'Treneva',
            ],
            [
                'id' => 3,
                'email' => 'amerkel@germany.de',
                'firstname' => 'Alina',
                'lastname' => 'Merkel',
            ],
            [
                'id' => 5,
                'email' => 'john.doe@usa.us',
                'firstname' => 'John',
                'lastname' => 'Doe',
            ],
        ],
        'currentUserId' => 1,
        'userDocCounts' => [
            1 => 27,
            2 => 8,
            3 => 0,
            5 => 0,
        ],
    ]);
});

Route::get('/about', function () {
    return 'about Page';
});
