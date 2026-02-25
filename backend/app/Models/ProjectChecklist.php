<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectChecklist extends Model
{
    protected $fillable = [
        'project_id',
        'checklist_id',
        'version',
        'status',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }

    public function items()
    {
        return $this->hasMany(ProjectChecklistItem::class);
    }
}
