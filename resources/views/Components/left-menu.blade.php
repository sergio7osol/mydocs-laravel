<aside class="sidebar">
    <h2 class="sidebar__title">Categories</h2>
    <ul class="category-tree">
     @php
         $currentCategoryId = request()->routeIs('categories.show') ? optional(request()->route('category'))->id : null;

         $renderTree = function(array $nodes, int $level = 0) use (&$renderTree, $currentCategoryId) {
             foreach ($nodes as $cat) {
                 $hasChildren = !empty($cat['children']);
                 echo '<li class="category-tree__item'.(!empty($cat['parent_id']) ? ' category-tree__subcategory' : '').'" data-id="'.e($cat['id']).'" data-level="'.$level.'">';
                 echo '<div class="category-tree__item-content">';
                 if ($hasChildren) {
                     echo '<span class="category-tree__toggle category-tree__toggle--expanded" data-id="'.e($cat['id']).'">▼</span>';
                 }
                 $isActive = $currentCategoryId !== null && (int)$currentCategoryId === (int)$cat['id'];
                 $linkClass = 'category-tree__link' . ($isActive ? ' category-tree__link--active' : '');
                 $href = route('categories.show', $cat['id']);
                 $count = isset($cat['doc_count']) ? (int)$cat['doc_count'] : 0;
                 echo '<a href="'.e($href).'" class="'.$linkClass.'">'.e($cat['name']).' <span class="category-count">'.e($count).'</span></a>';
                 echo '</div>';
                 if ($hasChildren) {
                     echo '<ul class="category-tree__subcategories category-tree__subcategories--expanded">';
                     $renderTree($cat['children'], $level + 1);
                     echo '</ul>';
                 }
                 echo '</li>';
             }
         };
     @endphp

     @php $renderTree($categories, 0); @endphp

     <li class="category-tree__add-item">
         <a href="{{ route('categories.create') }}" class="category-tree__add-link category-tree__add-link--inline">➕</a>
         <a href="{{ route('categories.index') }}" class="category-tree__add-link category-tree__add-link--inline">Categories management</a>
     </li>
    </ul>
</aside>
