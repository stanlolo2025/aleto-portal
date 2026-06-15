<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryListItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'beneficiary_list_id',
        'villager_record_id',
        'grant_amount',
        'duplicate_flagged',
        'reviewed_not_duplicate',
        'review_justification',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'grant_amount' => 'decimal:2',
            'duplicate_flagged' => 'boolean',
            'reviewed_not_duplicate' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function beneficiaryList()
    {
        return $this->belongsTo(BeneficiaryList::class);
    }

    public function villagerRecord()
    {
        return $this->belongsTo(VillagerRecord::class);
    }
}
