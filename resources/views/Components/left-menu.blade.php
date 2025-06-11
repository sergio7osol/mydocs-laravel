<aside class="sidebar">
    <h2 class="sidebar__title">Categories</h2>
    <ul class="category-tree">

        <li class="category-tree__item">

            <div class="category-tree__item-content">
                <span class="category-tree__toggle category-tree__toggle--expanded">
                    ▼
                </span>

                <a href="/?category=1" class="category-tree__link">
                    Category
                    <span class="category-count">0</span>
                </a>
            </div>


        <x-nav-link href="/categories" :active="request()->is('/categories')">Category home</x-nav-link>

        <li class="category-tree__add-item">
            <a href="/categories2" id="add-category-btn" class="category-tree__add-link {{request()->is('/about') ? 'hidden' : ''}}">
                <span class="purple-plus">➕</span> Category 2 <span class="delete-icon">❌</span>
            </a>
        </li>
        <li class="category-tree__add-item">
            <a href="/categories3" id="add-category-btn" class="category-tree__add-link">
                <span class="purple-plus">➕</span> Category 3 <span class="delete-icon">❌</span>
            </a>
        </li>
    </ul>
</aside>
