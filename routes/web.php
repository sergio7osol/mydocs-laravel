<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Document;

Route::get('/', function ()  {
	return view('home', [
		'pageTitle' => 'Documents list',
		'documents' => Document::all(),
		'users' => User::all(),
		'currentUserId' => 1,
		'userDocCounts' => [
			1 => 27,
			2 => 8,
			3 => 0,
			5 => 0,
		],
		'currentCategory' => null,
	]);
});

Route::get('/users/{id}', function ($id) {
	$user = User::find($id);

	return view('user-details', [
		'pageTitle' => 'User details',
		'user' => $user,
	]);
});

Route::get('/register', function () {
	return view('register', [
		'pageTitle' => 'Register',
		'users' => [],
		'currentUserId' => null,
		'userDocCounts' => [],
		'currentCategory' => '',
	]);
});
