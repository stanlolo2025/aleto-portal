<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class VillagerRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'unique_id',
        'full_name',
        'date_of_birth',
        'gender',
        'household_id',
        'village',
        'ward',
        'zone',
        'passport_photo',
        'nin',
        'phone_number',
        'email',
        'bank_account_number',
        'bank_name',
        'marital_status',
        'occupation',
        'education_level',
        'health_status',
        'nin_verification_status',
        'status',
        'date_of_death',
        'archive_reason',
        'registered_by',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'date_of_death' => 'date',
        ];
    }

    // Encrypt sensitive fields
    public function setPhoneNumberAttribute($value): void
    {
        $this->attributes['phone_number'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getPhoneNumberAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setBankAccountNumberAttribute($value): void
    {
        $this->attributes['bank_account_number'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getBankAccountNumberAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setNinAttribute($value): void
    {
        $this->attributes['nin'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getNinAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    // Relationships
    public function biometricData()
    {
        return $this->hasOne(BiometricData::class);
    }

    public function history()
    {
        return $this->hasMany(VillagerHistory::class)->orderBy('created_at', 'desc');
    }

    public function proxyAccount()
    {
        return $this->hasOne(ProxyAccount::class);
    }

    public function beneficiaryItems()
    {
        return $this->hasMany(BeneficiaryListItem::class);
    }

    public function familyMembers()
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function registeredByUser()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // Helpers
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isDeceased(): bool
    {
        return $this->status === 'deceased';
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    public function getEffectiveBankAccount(): ?string
    {
        if ($this->proxyAccount) {
            return $this->proxyAccount->proxy_bank_account;
        }
        return $this->bank_account_number;
    }
}
