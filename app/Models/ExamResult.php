<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExamResult extends Model
{
    protected $fillable = [
        'exam_id', 'student_id', 'subject', 'score', 'grade',
        'exam_date', 'exam_type', 'remarks', 'recorded_by',
    ];

    protected function casts(): array
    {
        return ['exam_date' => 'date'];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->exam_id)) {
                $model->exam_id = 'EX-' . strtoupper(Str::random(8));
            }
        });
    }

    public function student() { return $this->belongsTo(StudentRegistry::class, 'student_id'); }
    public function recordedByUser() { return $this->belongsTo(User::class, 'recorded_by'); }
}
