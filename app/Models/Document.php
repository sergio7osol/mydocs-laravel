<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Category;

class Document extends Model {
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'filename',
        'file_path',
        'file_size',
        'file_type',
        'user_id',
        'created_date',
        'description',
        'category_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'category_id' => 'integer',
        'file_size' => 'integer',
        'created_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the document
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that the document belongs to
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get human readable file size
     */
    public function getFileSizeHumanAttribute()
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = $this->file_size;
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if the document is a specific file type
     */
    public function isFileType($type)
    {
        return stripos($this->file_type, $type) !== false;
    }

    /**
     * Scope to filter by file type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('file_type', 'like', "%{$type}%");
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
