<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentRunItem extends Model
{
    protected $fillable = [
        'payment_run_id', 'villager_record_id', 'amount', 'status',
        'bank_name', 'account_number', 'transaction_reference', 'paid_at', 'failure_reason', 'confirmed_by',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'paid_at' => 'datetime'];
    }

    public function paymentRun() { return $this->belongsTo(PaymentRun::class); }
    public function villagerRecord() { return $this->belongsTo(VillagerRecord::class); }
    public function confirmedByUser() { return $this->belongsTo(User::class, 'confirmed_by'); }
}
