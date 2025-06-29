<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Document;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

Route::get('/documents', function () {
	$documents = Document::with('category')->latest()->paginate(5);

	return view('documents.index', [
		'pageTitle' => 'Documents list',
		'documents' => $documents,
		'users' => User::all(),
		'currentUserId' => 1,
		'userDocCounts' => $documents->pluck('user_id', 'documents_count')->toArray(),
		'currentCategory' => null,
	]);
})->name('documents.index');

Route::get('/documents/create', function () {
	return view('documents.create', [
		'pageTitle' => 'Create document',
		'users' => User::all(),
		'currentUserId' => 1,
		'categories' => Category::pluck('name', 'id'),
		'currentCategory' => null,
	]);
})->name('documents.create');

Route::get('/documents/{id}', function ($id) {
	$document = Document::find($id);

	return view('documents.show', [
		'pageTitle' => 'Document details',
		'document' => $document,
	]);
});

Route::post('/documents', function (Request $request) {
	$validated = $request->validate([
		'title'        => 'required|string|max:70',
		'description'  => 'nullable|string|max:300',
		'created_date' => 'nullable|date',
		'category_id'  => 'required|exists:categories,id',
		'user_id'      => 'required|exists:users,id',
		'document'     => 'required|file|mimes:pdf,doc,docx,txt,xls,xlsx,jpg,png,gif|max:15360', // 15 MB max
	]);

	try {
		// Handle file upload
		$file = $request->file('document');
		$filename = time() . '_' . $file->getClientOriginalName();
		$path = $file->storeAs('documents', $filename, 'public'); // Store in storage/app/public/documents

		// Create the document record
		Document::create([
			'title'        => $validated['title'],
			'description'  => $validated['description'],
			'created_date' => $validated['created_date'],
			'category_id'  => $validated['category_id'],
			'user_id'      => $validated['user_id'],
			'filename'     => $filename,
			'file_path'    => $path,
			'file_size'    => $file->getSize(),
			'file_type'    => $file->getMimeType(),
		]);

		return redirect()->route('documents.index')->with('message', 'Document uploaded successfully!');
	} catch (\Exception $e) {
		return redirect()->back()->withErrors(['document' => 'File upload failed: ' . $e->getMessage()])->withInput();
	}
});

Route::get('/users/{id}', function ($id) {
	$user = User::find($id);

	return view('users.show', [
		'pageTitle' => 'User details',
		'user' => $user,
	]);
});

Route::get('/register', function () {
	return view('users.register', [
		'pageTitle' => 'Register',
		'users' => [],
		'currentUserId' => null,
		'userDocCounts' => [],
		'currentCategory' => '',
	]);
});
