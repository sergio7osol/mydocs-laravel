<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\CategoryController;

Route::resource('documents', DocumentController::class);

Route::resource('categories', CategoryController::class)->except(['show']);
// Categories: show documents within a category (numeric id only to avoid conflicts with create/edit)
Route::get('/categories/{category}', [CategoryController::class, 'show'])
    ->whereNumber('category')
    ->name('categories.show');

Route::get('/documents/{doc}/download', function (Document $doc) {

	// Check if file exists
	if (!$doc->file_path || !Storage::disk('public')->exists($doc->file_path)) {
		return redirect()->back()->withErrors(['document' => 'File not found on server']);
	}

	// Return file download response
	return response()->download(Storage::disk('public')->path($doc->file_path), $doc->filename);
})->name('documents.download');

Route::get('/users/{user}', function (User $user) {
	return view('users.show', [
		'pageTitle' => 'User details',
		'user' => $user,
	]);
})->name('users.show');

Route::view('/register', 'register', [
	'pageTitle' => 'Register',
	'users' => [],
	'currentUserId' => null,
	'userDocCounts' => [],
	'currentCategory' => '',
])->name('register');
