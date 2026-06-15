<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Enquiry extends Model
{
    protected $fillable = [
        'ticket_id', 'full_name', 'email', 'phone', 'type', 'subject',
        'message', 'status', 'admin_response', 'responded_by', 'responded_at',
    ];

    protected function casts(): array
    {
        return ['responded_at' => 'datetime'];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->ticket_id)) {
                $model->ticket_id = 'TKT-' . strtoupper(Str::random(8));
            }
        });
    }

    public function respondedByUser() { return $this->belongsTo(User::class, 'responded_by'); }
}
