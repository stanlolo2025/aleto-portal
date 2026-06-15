<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Scholarship extends Model
{
    protected $fillable = [
        'scholarship_id', 'student_id', 'scholarship_type', 'amount',
        'payment_method', 'approval_status', 'approved_by', 'approved_at',
        'academic_year', 'remarks', 'created_by',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'approved_at' => 'datetime'];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->scholarship_id)) {
                $model->scholarship_id = 'SCH-' . strtoupper(Str::random(8));
            }
        });
    }

    public function student() { return $this->belongsTo(StudentRegistry::class, 'student_id'); }
    public function approvedByUser() { return $this->belongsTo(User::class, 'approved_by'); }
    public function createdByUser() { return $this->belongsTo(User::class, 'created_by'); }
}
