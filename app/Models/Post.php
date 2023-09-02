<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'audience', 'content', 'image'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    public function reactedUsers()
    {
        return $this->belongsToMany(User::class, 'reactions', 'post_id', 'user_id')
            ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function post_report()
    {
        return $this->hasMany(Post_Report::class);
    }

}
