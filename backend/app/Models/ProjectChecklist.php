<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectChecklist extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'project_id',
        'checklist_id',
        'version',
        'status',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(Checklist::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProjectChecklistItem::class)->orderBy('order');
    }

    public function getProgressPercentAttribute(): float
    {
        $total = $this->items()->count();
        if ($total === 0) return 0;

        $done = $this->items()
            ->whereIn('status', ['passed', 'failed', 'blocked'])
            ->count();

        return round(($done / $total) * 100, 2);
    }
}