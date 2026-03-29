<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'role',
        'action',
        'target_type',
        'target_id',
        'ip_address',
        'user_agent',
        'success',
        'metadata',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'success' => 'boolean',
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
