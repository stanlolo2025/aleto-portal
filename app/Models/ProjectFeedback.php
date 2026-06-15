<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProjectFeedback extends Model
{
    protected $table = 'project_feedback';
    protected $fillable = ['feedback_id', 'project_id', 'villager_record_id', 'feedback_text', 'date_submitted', 'recorded_by'];

    protected function casts(): array { return ['date_submitted' => 'date']; }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->feedback_id)) $model->feedback_id = 'FB-' . strtoupper(Str::random(8));
        });
    }

    public function project() { return $this->belongsTo(Project::class); }
    public function villagerRecord() { return $this->belongsTo(VillagerRecord::class); }
    public function recordedByUser() { return $this->belongsTo(User::class, 'recorded_by'); }
}
