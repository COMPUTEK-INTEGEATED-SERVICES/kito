<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'email_token', 'sms_token', 'email_token_used_at', 'sms_token_used_at'
    ];
}
