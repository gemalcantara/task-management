<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes; 
use App\Observers\TaskObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;


#[ObservedBy([TaskObserver::class])]
class Task extends Model
{
    use HasFactory, SoftDeletes;

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
    public function scopeOwnTask($query){
        return $query->where('user_id', auth()->id());
    }
    public function scopeMainTask($query)
    {
        return $query->whereNull('parent_id');
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
    
    /**
     * Calculate the progress of subtasks completion
     *
     * @return array
     */
    public function getSubtaskProgress()
    {
        $subtasks = $this->subtask()->ownTask()->get();
        
        if ($subtasks->count() === 0) {
            return [
                'total' => 0,
                'completed' => 0,
                'percent' => 0
            ];
        }
        
        $completed = $subtasks->where('status', 'done')->count();
        $total = $subtasks->count();
        $percent = ($total > 0) ? round(($completed / $total) * 100) : 0;
        
        return [
            'total' => $total,
            'completed' => $completed,
            'percent' => $percent
        ];
    }
    
    /**
     * Check if all subtasks are marked as done
     *
     * @return bool
     */
    public function areAllSubtasksDone()
    {
        $subtasks = $this->subtask()->ownTask()->get();
        
        if ($subtasks->count() === 0) {
            return false;
        }
        
        return $subtasks->where('status', '!=', 'done')->count() === 0;
    }
}
