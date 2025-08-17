<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Document;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
  public function index() {
    $documents = Document::with('category')->latest()->paginate(5);

    return view('documents.index', [
      'pageTitle' => 'Documents list',
      'documents' => $documents,
      'users' => User::all(),
      'currentUserId' => Auth::id(),
      'userDocCounts' => $documents->pluck('user_id', 'documents_count')->toArray(),
      'currentCategory' => null,
      'currentCategoryId' => null,
    ]);
  }

  public function create(Request $request) {
    // Determine preselected category id from query (?category_id=...) or fallback from ?category= (id or name)
    $selectedCategoryId = null;
    if ($request->filled('category_id')) {
      $selectedCategoryId = (int) $request->input('category_id');
    } elseif ($request->filled('category')) {
      $param = $request->input('category');
      if (is_numeric($param)) {
        $selectedCategoryId = (int) $param;
      } else {
        $selectedCategoryId = Category::where('name', $param)->value('id');
      }
    }

    return view('documents.create', [
      'pageTitle' => 'Create document',
      'users' => User::all(),
      'currentUserId' => Auth::id(),
      'categories' => Category::pluck('name', 'id'),
      'currentCategory' => null,
      'selectedCategoryId' => $selectedCategoryId,
    ]);
  }

  public function show(Document $document) {
    // Build paths/URLs for UI copy and view actions
    $filePublicUrl = asset('storage/' . ltrim($document->file_path ?? '', '/'));
    $windowsFilePath = $document->file_path
      ? Storage::disk('public')->path($document->file_path)
      : null;
    $windowsDirectoryPath = $windowsFilePath ? dirname($windowsFilePath) : null;
    $displayPath = 'storage/' . ltrim($document->file_path ?? '', '/');

    return view('documents.show', [
      'pageTitle' => 'Document details',
      'document' => $document,
      'users' => User::all(),
      'userDocCounts' => [],
      'documents' => collect([$document]),
      'filePublicUrl' => $filePublicUrl,
      'windowsFilePath' => $windowsFilePath,
      'windowsDirectoryPath' => $windowsDirectoryPath,
      'displayPath' => $displayPath,
    ]);
  }

  public function store(Request $request) {
    $validated = $request->validate([
      'title'        => 'required|string|max:70',
      'description'  => 'nullable|string|max:300',
      'created_date' => 'nullable|date',
      'category_id'  => 'required|exists:categories,id',
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
        'user_id'      => Auth::id(),
        'filename'     => $filename,
        'file_path'    => $path,
        'file_size'    => $file->getSize(),
        'file_type'    => $file->getMimeType(),
      ]);
  
      return redirect()->route('documents.index')->with('message', 'Document uploaded successfully!');
    } catch (\Exception $e) {
      return redirect()->back()->withErrors(['document' => 'File upload failed: ' . $e->getMessage()])->withInput();
    }
  }

  public function edit(Document $document) {
    return view('documents.edit', [
      'pageTitle' => 'Edit Document',
      'document' => $document,
      'users' => User::all(),
      'categories' => Category::pluck('name', 'id'),
      'currentUserId' => Auth::id(),
    ]);
  }

  public function update(Request $request, Document $document) {
    Gate::authorize('edit-document', $document);
    
    $validated = $request->validate([
      'title'        => 'required|string|max:70',
      'description'  => 'nullable|string|max:300',
      'created_date' => 'nullable|date',
      'category_id'  => 'required|exists:categories,id',
      'document'     => 'nullable|file|mimes:pdf,doc,docx,txt,xls,xlsx,jpg,png,gif|max:15360', // Optional for updates
    ]);
  
    try {
      // Update basic fields
      $document->title = $validated['title'];
      $document->description = $validated['description'];
      $document->created_date = $validated['created_date'];
      $document->category_id = $validated['category_id'];
  
      // Handle file replacement if new file uploaded
      if ($request->hasFile('document')) {
        // Delete old file if it exists
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
          Storage::disk('public')->delete($document->file_path);
        }
  
        // Upload new file
        $file = $request->file('document');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('documents', $filename, 'public');
  
        $document->filename = $filename;
        $document->file_path = $path;
        $document->file_size = $file->getSize();
        $document->file_type = $file->getMimeType();
      }
  
      $document->save();
  
      return redirect()->route('documents.show', $document)->with('message', 'Document updated successfully!');
    } catch (\Exception $e) {
      return redirect()->back()->withErrors(['document' => 'Update failed: ' . $e->getMessage()])->withInput();
    }
  }

  public function destroy(Document $document) {
    Gate::authorize('delete-document', $document);
    
    $document->delete();

    return redirect()->route('documents.index')->with('message', 'Document deleted successfully!');
  }
}
