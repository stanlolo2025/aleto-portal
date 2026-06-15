<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentRun extends Model
{
    protected $fillable = [
        'run_id', 'beneficiary_list_id', 'status', 'total_beneficiaries',
        'paid_count', 'failed_count', 'total_amount', 'paid_amount', 'initiated_by', 'completed_at',
    ];

    protected function casts(): array
    {
        return ['total_amount' => 'decimal:2', 'paid_amount' => 'decimal:2', 'completed_at' => 'datetime'];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->run_id)) $model->run_id = 'PAY-' . strtoupper(Str::random(8));
        });
    }

    public function beneficiaryList() { return $this->belongsTo(BeneficiaryList::class); }
    public function items() { return $this->hasMany(PaymentRunItem::class); }
    public function initiatedByUser() { return $this->belongsTo(User::class, 'initiated_by'); }
}
