<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ImpactAnalytic extends Model
{
    protected $table = 'impact_analytics';
    protected $fillable = ['impact_id', 'project_id', 'metric', 'value', 'date_recorded', 'notes', 'recorded_by'];

    protected function casts(): array { return ['date_recorded' => 'date', 'value' => 'decimal:2']; }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->impact_id)) $model->impact_id = 'IMP-' . strtoupper(Str::random(8));
        });
    }

    public function project() { return $this->belongsTo(Project::class); }
    public function recordedByUser() { return $this->belongsTo(User::class, 'recorded_by'); }
}
