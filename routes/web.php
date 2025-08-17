<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionController;

// Documents (explicit routes instead of resource)
Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create')->middleware('auth');
Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store')->middleware('auth');
Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
Route::get('/documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit')->middleware('auth')->can('edit-document', 'document');
Route::patch('/documents/{document}', [DocumentController::class, 'update'])->name('documents.update')->middleware('auth')->can('edit-document', 'document');
Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy')->middleware('auth')->can('delete-document', 'document');

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
})->middleware('auth')->name('documents.download');

Route::get('/users/{user}', function (User $user) {
	return view('users.show', [
		'pageTitle' => 'User details',
		'user' => $user,
	]);
})->name('users.show');

// Auth
Route::get('/register', [RegisteredUserController::class, 'create'])->name('auth.register');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('auth.register');
	
Route::get('/login', [SessionController::class, 'create'])->name('auth.login');
Route::post('/login', [SessionController::class, 'store'])->name('auth.login');
Route::post('/logout', [SessionController::class, 'destroy'])->name('auth.logout');
