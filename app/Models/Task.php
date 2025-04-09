<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //
    protected $fillable = [
        'title',
        'content',
        'status',
        'visibility',
        'image',
        'user_id',
        'parent_id',
    ];
    protected $casts = [
        'status' => 'string',
        'visibility' => 'string',
        'user_id' => 'integer',
        'parent_id' => 'integer',
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }
    public function subtask()
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function scopeMainTask($query)
    {
        return $query->whereNotNull('parent_id');
    }
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image ? asset($this->image) : "https://placehold.co/600x300?text=Image\nUnavailable"
        );
    }

    protected function visibility(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
            set: fn (string $value) => strtolower($value),
        );
    }
    
    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
            if (empty($value)) {
                return $this->created_at->format('Y-m-d H:i:s');
            }
            return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
            },
        );
    }
    
}
