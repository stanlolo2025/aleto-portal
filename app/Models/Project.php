<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    protected $fillable = [
        'project_id', 'project_type', 'name', 'location', 'start_date',
        'end_date', 'status', 'description', 'budget', 'created_by',
    ];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date', 'budget' => 'decimal:2'];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->project_id)) $model->project_id = 'PRJ-' . strtoupper(Str::random(8));
        });
    }

    public function beneficiaries() { return $this->hasMany(ProjectBeneficiary::class); }
    public function feedback() { return $this->hasMany(ProjectFeedback::class); }
    public function impactAnalytics() { return $this->hasMany(ImpactAnalytic::class); }
    public function createdByUser() { return $this->belongsTo(User::class, 'created_by'); }
}
