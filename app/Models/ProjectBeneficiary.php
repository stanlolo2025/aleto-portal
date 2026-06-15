<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProjectBeneficiary extends Model
{
    protected $fillable = ['mapping_id', 'project_id', 'villager_record_id', 'benefit_type', 'remarks', 'recorded_by'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->mapping_id)) $model->mapping_id = 'BM-' . strtoupper(Str::random(8));
        });
    }

    public function project() { return $this->belongsTo(Project::class); }
    public function villagerRecord() { return $this->belongsTo(VillagerRecord::class); }
    public function recordedByUser() { return $this->belongsTo(User::class, 'recorded_by'); }
}
