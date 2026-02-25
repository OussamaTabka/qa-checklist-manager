<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Checklist extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'version',
        'priority',
        'criticality',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'priority' => 'integer',
            'criticality' => 'integer',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(ChecklistItem::class);
    }
}
