<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    protected $fillable = ['villager_record_id', 'full_name', 'relationship', 'date_of_birth', 'occupation'];

    protected function casts(): array
    {
        return ['date_of_birth' => 'date'];
    }

    public function villagerRecord() { return $this->belongsTo(VillagerRecord::class); }
}
