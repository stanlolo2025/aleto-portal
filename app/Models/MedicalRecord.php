<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MedicalRecord extends Model
{
    protected $fillable = [
        'record_id', 'villager_record_id', 'vaccination_type', 'vaccination_date',
        'vaccination_status', 'chronic_conditions', 'disability_status', 'allergies',
        'notes', 'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'vaccination_date' => 'date',
            'disability_status' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->record_id)) {
                $model->record_id = 'MR-' . strtoupper(Str::random(8));
            }
        });
    }

    public function villagerRecord() { return $this->belongsTo(VillagerRecord::class); }
    public function recordedByUser() { return $this->belongsTo(User::class, 'recorded_by'); }
}
