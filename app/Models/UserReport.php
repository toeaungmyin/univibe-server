<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'compliant_id',
        'resistant_id',
        'title',
        'description',
    ];

    // In your User model
    public function compliant()
    {
        return $this->belongsTo(User::class, 'compliant_id');
    }

    // In your UserReport model
    public function resistant()
    {
        return $this->belongsTo(User::class, 'resistant_id');
    }

}
