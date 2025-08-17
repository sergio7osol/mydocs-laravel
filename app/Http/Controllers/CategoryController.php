<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display the documents for a given category.
     */
    public function show(Category $category, Request $request)
    {
        // Collect this category and all descendants using the id-chain in `path` (e.g., "3/5/")
        // Match either at the start ("5/%") or after a slash ("%/5/%") to be robust for any level.
        $descendantIds = Category::query()
            ->where(function ($q) use ($category) {
                $q->where('path', 'like', $category->id . '/%')
                  ->orWhere('path', 'like', '%/' . $category->id . '/%');
            })
            ->pluck('id');

        $categoryIds = $descendantIds->push($category->id)->unique()->all();

        $documents = Document::with('category')
            ->whereIn('category_id', $categoryIds)
            ->latest()
            ->paginate(5);

        return view('documents.index', [
            'pageTitle' => 'Category: ' . $category->name,
            'documents' => $documents,
            'users' => User::all(),
            'currentUserId' => 1,
            'userDocCounts' => [],
            // For display texts in views, keep the human-readable name
            'currentCategory' => $category->name,
            'currentCategoryId' => $category->id,
        ]);
    }

    /**
     * Categories index (basic CRUD list)
     */
    public function index()
    {
        $flat = Category::query()
            ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id', 'path', 'level', 'is_active', 'display_order'])
            ->toArray();

        $tree = $this->buildTree($flat);
        $categoriesFlat = [];
        $this->flattenTree($tree, $categoriesFlat);

        return view('categories.index', [
            'pageTitle' => 'Categories',
            'categories' => $categoriesFlat,
        ]);
    }

    /**
     * Show create form
     */
    public function create(Request $request)
    {
        $selectedParentId = $request->integer('parent_id');

        $parents = Category::orderBy('level')->orderBy('display_order')->orderBy('name')->get();

        return view('categories.create', [
            'pageTitle' => 'Create Category',
            'parents' => $parents,
            'selectedParentId' => $selectedParentId,
        ]);
    }

    /**
     * Store new category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('categories')->where(function ($q) use ($request) {
                    return $q->where('parent_id', $request->input('parent_id'));
                }),
            ],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'is_active' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer'],
        ]);

        $parent = null;
        if (!empty($validated['parent_id'])) {
            $parent = Category::find($validated['parent_id']);
        }

        [$path, $level] = $this->computePathAndLevel($parent);

        $category = Category::create([
            'name' => $validated['name'],
            'user_id' => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'path' => $path,
            'level' => $level,
            'is_active' => (bool)($validated['is_active'] ?? true),
            'display_order' => $validated['display_order'] ?? 0,
        ]);

        return redirect()->route('categories.index')->with('message', 'Category created');
    }

    /**
     * Edit category form
     */
    public function edit(Category $category)
    {
        // Disallow selecting self or descendants as parent
        $invalidIds = $this->selfAndDescendantIds($category);
        $parents = Category::whereNotIn('id', $invalidIds)
            ->orderBy('level')->orderBy('display_order')->orderBy('name')->get();

        return view('categories.edit', [
            'pageTitle' => 'Edit Category',
            'category' => $category,
            'parents' => $parents,
        ]);
    }

    /**
     * Update category
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('categories')->ignore($category->id)->where(function ($q) use ($request) {
                    return $q->where('parent_id', $request->input('parent_id'));
                }),
            ],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'is_active' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer'],
        ]);

        $oldPath = $category->path; // before change
        $oldPrefix = ($oldPath ? $oldPath : '') . $category->id . '/';

        $parent = null;
        if (!empty($validated['parent_id'])) {
            $parent = Category::find($validated['parent_id']);
        }

        [$newPath, $newLevel] = $this->computePathAndLevel($parent);

        $category->name = $validated['name'];
        $category->parent_id = $validated['parent_id'] ?? null;
        $category->path = $newPath;
        $category->level = $newLevel;
        $category->is_active = (bool)($validated['is_active'] ?? true);
        $category->display_order = $validated['display_order'] ?? 0;
        $category->save();

        // Update descendants' paths and levels if parent path changed
        $newPrefix = ($newPath ? $newPath : '') . $category->id . '/';
        if ($oldPrefix !== $newPrefix) {
            $this->rebuildDescendantPaths($oldPrefix, $newPrefix);
        }

        return redirect()->route('categories.index')->with('message', 'Category updated');
    }

    /**
     * Delete category (basic rules: no children, no documents)
     */
    public function destroy(Category $category)
    {
        $hasChildren = Category::where('parent_id', $category->id)->exists();
        $hasDocs = $category->documents()->exists();
        if ($hasChildren || $hasDocs) {
            return redirect()->back()->withErrors(['category' => 'Cannot delete: category has ' . ($hasChildren ? 'children' : 'documents') . '.']);
        }
        $category->delete();
        return redirect()->route('categories.index')->with('message', 'Category deleted');
    }

    // ---------- Helper methods ----------

    private function computePathAndLevel(?Category $parent): array
    {
        if ($parent === null) {
            return [null, 0];
        }
        $path = ($parent->path ? $parent->path : '') . $parent->id . '/';
        $level = $parent->level + 1;
        return [$path, $level];
    }

    private function selfAndDescendantIds(Category $category): array
    {
        $prefix = ($category->path ? $category->path : '') . $category->id . '/';
        $descendants = Category::where('path', 'like', $prefix . '%')->pluck('id')->all();
        $descendants[] = $category->id;
        return $descendants;
    }

    private function rebuildDescendantPaths(string $oldPrefix, string $newPrefix): void
    {
        $descendants = Category::where('path', 'like', $oldPrefix . '%')->get();
        foreach ($descendants as $desc) {
            $newPath = preg_replace('/^' . preg_quote($oldPrefix, '/') . '/', $newPrefix, (string)$desc->path);
            $desc->path = $newPath;
            $desc->level = substr_count((string)$newPath, '/');
            $desc->save();
        }
    }

    private function buildTree(array $flat): array
    {
        $map = [];
        foreach ($flat as $row) {
            $pid = $row['parent_id'] ?? 0;
            if (!array_key_exists($pid, $map)) {
                $map[$pid] = [];
            }
            $map[$pid][] = $row;
        }
        return $this->buildFromMap($map, 0);
    }

    private function buildFromMap(array $map, int $parentKey): array
    {
        $list = $map[$parentKey] ?? [];
        foreach ($list as &$node) {
            $node['children'] = $this->buildFromMap($map, (int)$node['id']);
        }
        unset($node);
        return $list;
    }

    private function flattenTree(array $nodes, array &$out, int $level = 0): void
    {
        foreach ($nodes as $n) {
            $out[] = [
                'id' => $n['id'],
                'name' => $n['name'],
                'parent_id' => $n['parent_id'],
                'is_active' => $n['is_active'],
                'display_order' => $n['display_order'],
                'level' => $level,
            ];
            if (!empty($n['children'])) {
                $this->flattenTree($n['children'], $out, $level + 1);
            }
        }
    }
}
