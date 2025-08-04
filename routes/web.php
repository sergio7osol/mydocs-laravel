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

Route::get('/documents/{doc}', function (Document $doc) {

	return view('documents.show', [
		'pageTitle' => 'Document details',
		'document' => $doc,
		'users' => User::all(),
		'userDocCounts' => [],
		'documents' => collect([$doc]),
	]);
})->name('documents.show');

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

Route::get('/documents/{doc}/edit', function (Document $doc) {
	return view('documents.edit', [
		'pageTitle' => 'Edit Document',
		'document' => $doc,
		'users' => User::all(),
		'categories' => Category::pluck('name', 'id'),
	]);
})->name('documents.edit');

// Update document route
Route::patch('/documents/{doc}', function (Request $request, Document $doc) {

	$validated = $request->validate([
		'title'        => 'required|string|max:70',
		'description'  => 'nullable|string|max:300',
		'created_date' => 'nullable|date',
		'category_id'  => 'required|exists:categories,id',
		'user_id'      => 'required|exists:users,id',
		'document'     => 'nullable|file|mimes:pdf,doc,docx,txt,xls,xlsx,jpg,png,gif|max:15360', // Optional for updates
	]);

	try {
		// Update basic fields
		$doc->title = $validated['title'];
		$doc->description = $validated['description'];
		$doc->created_date = $validated['created_date'];
		$doc->category_id = $validated['category_id'];
		$doc->user_id = $validated['user_id'];

		// Handle file replacement if new file uploaded
		if ($request->hasFile('document')) {
			// Delete old file if it exists
			if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
				Storage::disk('public')->delete($doc->file_path);
			}

			// Upload new file
			$file = $request->file('document');
			$filename = time() . '_' . $file->getClientOriginalName();
			$path = $file->storeAs('documents', $filename, 'public');

			$doc->filename = $filename;
			$doc->file_path = $path;
			$doc->file_size = $file->getSize();
			$doc->file_type = $file->getMimeType();
		}

		$doc->save();

		return redirect()->route('documents.show', $doc)->with('message', 'Document updated successfully!');
	} catch (\Exception $e) {
		return redirect()->back()->withErrors(['document' => 'Update failed: ' . $e->getMessage()])->withInput();
	}
})->name('documents.update');

Route::delete('/documents/{doc}', function (Document $doc) {
	$doc->delete();

	return redirect()->route('documents.index')->with('message', 'Document deleted successfully!');
})->name('documents.destroy');

// Download document route (temporary)
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

Route::get('/register', function () {
	return view('users.register', [
		'pageTitle' => 'Register',
		'users' => [],
		'currentUserId' => null,
		'userDocCounts' => [],
		'currentCategory' => '',
	]);
});
