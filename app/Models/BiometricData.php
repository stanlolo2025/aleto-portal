<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class BiometricData extends Model
{
    protected $table = 'biometric_data';

    protected $fillable = [
        'villager_record_id',
        'fingerprint_template',
        'facial_photo_path',
        'captured_at',
    ];

    protected function casts(): array
    {
        return [
            'captured_at' => 'datetime',
        ];
    }

    public function setFingerprintTemplateAttribute($value): void
    {
        $this->attributes['fingerprint_template'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getFingerprintTemplateAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setFacialPhotoPathAttribute($value): void
    {
        $this->attributes['facial_photo_path'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getFacialPhotoPathAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function villagerRecord()
    {
        return $this->belongsTo(VillagerRecord::class);
    }
}
