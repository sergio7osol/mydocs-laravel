<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Document;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
  public function index() {
    $documents = Document::with(['category', 'user'])->latest()->paginate(5);

    return view('documents.index', [
      'pageTitle' => 'Documents list',
      'documents' => $documents,
      'users' => User::all(),
      'currentUserId' => Auth::id(),
      // Document counts per user across all documents (for header badges)
      'userDocCounts' => Document::selectRaw('user_id, COUNT(*) as total')
        ->groupBy('user_id')
        ->pluck('total', 'user_id')
        ->toArray(),
      'currentCategory' => null,
      'currentCategoryId' => null,
      // On All Documents page, uploads are allowed (UI-wise) for authenticated users
      'canUploadToCurrentCategory' => Auth::check(),
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

    // Build categories list: admins see all; non-admins see only their own categories
    $categoriesQuery = Category::query();
    if (!Auth::user()?->is_admin) {
      $categoriesQuery->where('user_id', Auth::id());
    }
    $categories = $categoriesQuery->orderBy('name')->pluck('name', 'id');

    return view('documents.create', [
      'pageTitle' => 'Create document',
      'users' => User::all(),
      'currentUserId' => Auth::id(),
      'categories' => $categories,
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
    // Enforce category ownership for non-admins via validation
    $categoryRule = Auth::user()?->is_admin
      ? ['required', Rule::exists('categories', 'id')]
      : ['required', Rule::exists('categories', 'id')->where(fn ($q) => $q->where('user_id', Auth::id()))];

    $validated = $request->validate([
      'title'        => 'required|string|max:70',
      'description'  => 'nullable|string|max:300',
      'created_date' => 'nullable|date',
      'category_id'  => $categoryRule,
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
    // Build categories list: admins see all; non-admins see only their own categories
    $categoriesQuery = Category::query();
    if (!Auth::user()?->is_admin) {
      $categoriesQuery->where('user_id', Auth::id());
    }
    $categories = $categoriesQuery->orderBy('name')->pluck('name', 'id');
    // If user does not own the document's current category, still include it so they can keep it unchanged
    if (!Auth::user()?->is_admin && $document->category_id && !$categories->has($document->category_id)) {
      $currentName = Category::where('id', $document->category_id)->value('name');
      if ($currentName) {
        $categories = $categories->put($document->category_id, $currentName . ' (current)');
      }
    }

    return view('documents.edit', [
      'pageTitle' => 'Edit Document',
      'document' => $document,
      'users' => User::all(),
      'categories' => $categories,
      'currentUserId' => Auth::id(),
    ]);
  }

  public function update(Request $request, Document $document) {
    Gate::authorize('edit-document', $document);
    
    // Enforce category ownership for non-admins via validation (admin bypass)
    $categoryRule = Auth::user()?->is_admin
      ? ['required', Rule::exists('categories', 'id')]
      : ['required', Rule::exists('categories', 'id')->where(fn ($q) => $q->where('user_id', Auth::id()))];

    $validated = $request->validate([
      'title'        => 'required|string|max:70',
      'description'  => 'nullable|string|max:300',
      'created_date' => 'nullable|date',
      'category_id'  => $categoryRule,
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

  // Stream a file download response
  public function download(Document $document) {
    if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
      return redirect()->back()->withErrors(['document' => 'File not found on server']);
    }

    return response()->download(
      Storage::disk('public')->path($document->file_path),
      $document->filename
    );
  }

  // Stream a file inline view response
  public function viewFile(Document $document) {
    if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
      return redirect()->back()->withErrors(['document' => 'File not found on server']);
    }

    return response()->file(
      Storage::disk('public')->path($document->file_path)
    );
  }
}
