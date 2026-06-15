<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ClinicVisit extends Model
{
    protected $fillable = [
        'visit_id', 'villager_record_id', 'clinic_name', 'clinic_location',
        'visit_date', 'reason', 'treatment', 'health_worker', 'notes', 'recorded_by',
    ];

    protected function casts(): array
    {
        return ['visit_date' => 'date'];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->visit_id)) {
                $model->visit_id = 'CV-' . strtoupper(Str::random(8));
            }
        });
    }

    public function villagerRecord() { return $this->belongsTo(VillagerRecord::class); }
    public function recordedByUser() { return $this->belongsTo(User::class, 'recorded_by'); }
}
