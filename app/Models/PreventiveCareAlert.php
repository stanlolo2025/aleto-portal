<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PreventiveCareAlert extends Model
{
    protected $fillable = [
        'alert_id', 'villager_record_id', 'alert_type', 'description',
        'due_date', 'status', 'completed_date', 'created_by',
    ];

    protected function casts(): array
    {
        return ['due_date' => 'date', 'completed_date' => 'date'];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->alert_id)) {
                $model->alert_id = 'PCA-' . strtoupper(Str::random(8));
            }
        });
    }

    public function villagerRecord() { return $this->belongsTo(VillagerRecord::class); }
    public function createdByUser() { return $this->belongsTo(User::class, 'created_by'); }
}
