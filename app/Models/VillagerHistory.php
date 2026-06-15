<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillagerHistory extends Model
{
    public $timestamps = false;

    protected $table = 'villager_history';

    protected $fillable = [
        'villager_record_id',
        'field_name',
        'old_value',
        'new_value',
        'changed_by',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function villagerRecord()
    {
        return $this->belongsTo(VillagerRecord::class);
    }

    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
