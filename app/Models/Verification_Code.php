<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification_Code extends Model
{
    use HasFactory;
    protected $table = 'verification_codes';
    protected $fillable = [
        'user_id', 'code'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
