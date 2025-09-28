<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use App\Models\Document;
use App\Models\User;

class SearchController extends Controller
{
    public function __invoke()
    {
        $documents = Document::where('title', 'like', '%' . request('q') . '%')
            ->with(['category', 'user'])
            ->latest()
            ->paginate(5);

        return view('documents.index', [
            'pageTitle' => 'Search Results for "' . request('q') . '"',
            'documents' => $documents,
            'users' => User::all(),
            'currentUserId' => Auth::id(),
            'userDocCounts' => Document::selectRaw('user_id, COUNT(*) as total')
                ->groupBy('user_id')
                ->pluck('total', 'user_id')
                ->toArray(),
            'currentCategory' => null,
            'currentCategoryId' => null,
            'canUploadToCurrentCategory' => Auth::check(),
        ]);
    }
}
