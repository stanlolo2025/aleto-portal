<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StudentRegistry extends Model
{
    protected $table = 'student_registry';

    protected $fillable = [
        'student_id', 'villager_record_id', 'school_name', 'class_level',
        'enrollment_status', 'days_present', 'days_absent', 'registered_by',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->student_id)) {
                $model->student_id = 'STU-' . strtoupper(Str::random(8));
            }
        });
    }

    public function villagerRecord() { return $this->belongsTo(VillagerRecord::class); }
    public function registeredByUser() { return $this->belongsTo(User::class, 'registered_by'); }
    public function scholarships() { return $this->hasMany(Scholarship::class, 'student_id'); }
    public function examResults() { return $this->hasMany(ExamResult::class, 'student_id'); }
}
