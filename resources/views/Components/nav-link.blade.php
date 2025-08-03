@props(['active' => false])

<li class="category-tree__add-item">
    <a  
        {{ $attributes->merge(['class' => "category-tree__add-link " . ($active ? "hidden" : "")]) }}
        id="add-category-btn" 
        aria-current="{{ $active ? 'page' : 'false' }}"
    >
        <span class="purple-plus">➕</span> {{ $slot }} <span class="delete-icon">❌</span>
    </a>
</li>
