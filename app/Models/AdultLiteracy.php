<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AdultLiteracy extends Model
{
    protected $table = 'adult_literacy';

    protected $fillable = [
        'program_id', 'villager_record_id', 'program_name', 'enrollment_status',
        'sessions_attended', 'total_sessions', 'completion_status', 'start_date',
        'end_date', 'registered_by',
    ];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date'];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->program_id)) {
                $model->program_id = 'LIT-' . strtoupper(Str::random(8));
            }
        });
    }

    public function villagerRecord() { return $this->belongsTo(VillagerRecord::class); }
    public function registeredByUser() { return $this->belongsTo(User::class, 'registered_by'); }
}
