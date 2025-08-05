<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\DocumentController;

// index
Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');

// create
Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');

// show
Route::get('/documents/{doc}', [DocumentController::class, 'show'])->name('documents.show');

// store
Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');

// edit
Route::get('/documents/{doc}/edit', [DocumentController::class, 'edit'])->name('documents.edit');

// update
Route::patch('/documents/{doc}', [DocumentController::class, 'update'])->name('documents.update');

// delete
Route::delete('/documents/{doc}', [DocumentController::class, 'destroy'])->name('documents.destroy');


// download
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
