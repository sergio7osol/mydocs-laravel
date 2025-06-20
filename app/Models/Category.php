<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
        'path',
        'level',
        'is_active',
        'display_order'
    ];

    protected $casts = [
        'id' => 'integer',
        'parent_id' => 'integer',
        'level' => 'integer',
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Get the parent category
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Get the documents in this category
    public function documents() {
        return $this->hasMany(Document::class);
    }

    /**
     * Get all descendants (children, grandchildren, etc.)
     */
    public function descendants()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('descendants');
    }

    /**
     * Get all ancestors (parent, grandparent, etc.)
     */
    public function ancestors()
    {
        return $this->parent ? $this->parent->ancestors()->prepend($this->parent) : collect();
    }

    /**
     * Get the root category
     */
    public function root()
    {
        return $this->parent ? $this->parent->root() : $this;
    }

    /**
     * Check if this category is a root category
     */
    public function isRoot()
    {
        return is_null($this->parent_id);
    }

    /**
     * Check if this category has children
     */
    public function hasChildren()
    {
        return $this->children()->exists();
    }

    /**
     * Get the full category path as names
     */
    public function getFullPathAttribute()
    {
        $path = $this->ancestors()->pluck('name')->toArray();
        $path[] = $this->name;
        return implode(' > ', $path);
    }

    /**
     * Scope to get only root categories
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get categories at a specific level
     */
    public function scopeAtLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope to order by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }
}
