<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class PasswordResetOtp extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'otp'];

    public $timestamps = false;
}
