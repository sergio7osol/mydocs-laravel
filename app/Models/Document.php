<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Category;

class Document extends Model {
    use HasFactory;
    
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

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',  
        'category_id' => 'integer',
        'file_size' => 'integer',
        'created_date' => 'date',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function tags() {
        return $this->belongsToMany(Tag::class);
    }

    public function getFileSizeHumanAttribute(): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max(0, $this->file_size); // Prevent negative values
        
        if ($bytes === 0) {
            return '0 B';
        }
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isFileType(string $type): bool {
        return stripos($this->file_type, $type) !== false;
    }

    /** Scope to filter by file type */
    public function scopeOfType($query, string $type) {
        return $query->where('file_type', 'like', "%{$type}%");
    }

    /** Scope to filter by user */
    public function scopeByUser($query, int $userId) {
        return $query->where('user_id', $userId);
    }

    /** Resolve route binding with eager loaded relationships */
    public function resolveRouteBinding($value, $field = null) {
        return $this->with(['category', 'user'])->find($value);
    }
}
