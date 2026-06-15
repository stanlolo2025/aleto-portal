<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grant extends Model
{
    protected $fillable = [
        'grant_identifier',
        'name',
        'description',
        'amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function beneficiaryLists()
    {
        return $this->hasMany(BeneficiaryList::class);
    }

    public function approvedBeneficiaryLists()
    {
        return $this->hasMany(BeneficiaryList::class)->where('status', 'approved');
    }
}
