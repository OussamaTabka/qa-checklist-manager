<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistItem extends Model
{
    protected $fillable = [
        'checklist_id',
        'label',
        'description',
        'priority',
        'criticality',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'priority' => 'integer',
            'criticality' => 'integer',
            'order' => 'integer',
        ];
    }

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(Checklist::class);
    }
}
