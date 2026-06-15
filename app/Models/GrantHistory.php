<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GrantHistory extends Model
{
    protected $table = 'grant_history';

    protected $fillable = [
        'history_id',
        'grant_id',
        'villager_record_id',
        'action_type',
        'action_date',
        'amount',
        'payment_method',
        'transaction_reference',
        'performed_by',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'action_date' => 'datetime',
            'amount' => 'decimal:2',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->history_id)) {
                $model->history_id = 'GH-' . strtoupper(Str::random(8));
            }
        });
    }

    public function grant()
    {
        return $this->belongsTo(Grant::class);
    }

    public function villagerRecord()
    {
        return $this->belongsTo(VillagerRecord::class);
    }

    public function performedByUser()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
