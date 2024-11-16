<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InviteCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'is_used',
        'created_by',
        'used_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by');
    }
}
