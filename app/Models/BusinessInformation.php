<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class BusinessInformation extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'business_information';

    protected $fillable = [
        'user_id', // Make the user_id fillable
        'company_name',
        'company_id',
        'tax_identification_number',
        'company_email',
        'company_phone_number',
        'company_address',
    ];

    /**
     * Get the user that owns the business information.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
