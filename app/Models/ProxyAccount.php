<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ProxyAccount extends Model
{
    protected $fillable = [
        'villager_record_id',
        'representative_name',
        'relationship',
        'proxy_bank_name',
        'proxy_bank_account',
    ];

    public function setProxyBankAccountAttribute($value): void
    {
        $this->attributes['proxy_bank_account'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getProxyBankAccountAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function villagerRecord()
    {
        return $this->belongsTo(VillagerRecord::class);
    }

    public static function countByRepresentative(string $name): int
    {
        return static::where('representative_name', $name)->count();
    }
}
