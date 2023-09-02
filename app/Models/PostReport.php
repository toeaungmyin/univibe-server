<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostReport extends Model
{
    use HasFactory;
    protected $fillable = [
        'compliant_id',
        'resistant_id',
        'title',
        'description',
        'post_id'
    ];

    function post()
    {
        return $this->belongsTo(Post::class);
    }

    function compliant()
    {
        return $this->belongsTo(User::class, 'post_reports', 'resistant_id', 'compliant_id');
    }

    function resistant()
    {
        return $this->belongsTo(User::class, 'post_reports', 'compliant_id', 'resistant_id');
    }
}
