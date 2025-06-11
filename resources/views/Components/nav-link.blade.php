@props(['active' => false])

<li class="category-tree__add-item">
    <a  
        {{ $attributes }}
        id="add-category-btn" 
        class="category-tree__add-link {{ $active ? 'hidden' : '' }}"
        aria-current="{{ $active ? 'page' : 'false' }}"
    >
        <span class="purple-plus">➕</span> {{ $slot }} <span class="delete-icon">❌</span>
    </a>
</li>
