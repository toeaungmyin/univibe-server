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

    function compliant()
    {
        return $this->belongsTo(User::class, 'post_reports', 'resistant_id', 'compliant_id');
    }

    function resistant()
    {
        return $this->belongsTo(User::class, 'post_reports', 'compliant_id', 'resistant_id');
    }
}
