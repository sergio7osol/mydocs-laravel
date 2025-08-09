<?php

namespace App\View\Components;

use App\Models\Category;
use App\Models\Document;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LeftMenu extends Component
{
    /**
     * The nested categories tree.
     *
     * @var array<int, array<string, mixed>>
     */
    public array $categories;

    public function __construct()
    {
        // Fetch categories without hard-coding is_active. Order by display_order then name.
        $flat = Category::query()
            ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id', 'path', 'level', 'is_active', 'display_order'])
            ->toArray();

        // Build nested tree
        $this->categories = $this->buildTree($flat);

        // Compute per-category document counts (self only)
        $selfCounts = Document::selectRaw('category_id, COUNT(*) as cnt')
            ->groupBy('category_id')
            ->pluck('cnt', 'category_id')
            ->toArray();

        // Inject total counts (self + descendants) into each node as 'doc_count'
        $this->injectCounts($this->categories, $selfCounts);
    }

    /**
     * Build a nested tree from a flat category list using parent_id.
     * Keeps sibling ordering as provided by the query above.
     *
     * @param array<int, array<string, mixed>> $flat
     * @return array<int, array<string, mixed>>
     */
    private function buildTree(array $flat): array
    {
        // Group by parent_id (use 0 as the key for NULL roots to avoid null-key issues)
        $childrenMap = [];
        foreach ($flat as $row) {
            $pid = $row['parent_id'] ?? 0;
            if (!array_key_exists($pid, $childrenMap)) {
                $childrenMap[$pid] = [];
            }
            $childrenMap[$pid][] = $row;
        }

        return $this->buildFromMap($childrenMap, 0);
    }

    /**
     * @param array<int|string, array<int, array<string, mixed>>> $map
     * @param int $parentKey
     * @return array<int, array<string, mixed>>
     */
    private function buildFromMap(array $map, int $parentKey): array
    {
        $list = $map[$parentKey] ?? [];
        foreach ($list as &$node) {
            $node['children'] = $this->buildFromMap($map, (int) $node['id']);
        }
        unset($node);
        return $list;
    }

    /**
     * Recursively compute and inject total document counts for each node.
     * Adds 'doc_count' to each node. Returns the aggregate count for the list.
     *
     * @param array<int, array<string, mixed>> $nodes
     * @param array<int, int> $selfCounts
     * @return int
     */
    private function injectCounts(array &$nodes, array $selfCounts): int
    {
        $sum = 0;
        foreach ($nodes as &$node) {
            $childrenTotal = 0;
            if (!empty($node['children'])) {
                $childrenTotal = $this->injectCounts($node['children'], $selfCounts);
            }
            $own = (int)($selfCounts[$node['id']] ?? 0);
            $node['doc_count'] = $own + $childrenTotal;
            $sum += $node['doc_count'];
        }
        unset($node);
        return $sum;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        // Pass nested categories to the blade view
        return view('components.left-menu', [
            'categories' => $this->categories,
        ]);
    }
}
