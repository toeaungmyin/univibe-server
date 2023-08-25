<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Mail\PasswordResetEmail;
use App\Mail\VertifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Laravel\Passport\HasApiTokens;
use Nette\Utils\Random;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'birthday',
        'profile_url',
        'online',
        'email_verified',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public $guard_name = 'api';

    /*
     * Custom methods
     */

    //  email verification
    public function sendVertifyEmail()
    {
        try {
            if ($this->verification_code) {
                $verificationCode = Verification_Code::where('user_id', $this->id)->first();

                if ($verificationCode) {
                    $verificationCode->delete();
                }
            }

            $exist_codes = Verification_Code::pluck('code')->all();

            do {
                $code = Random::generate(6, '0-9');
            } while (in_array($code, $exist_codes));

            Verification_Code::create([
                'user_id' => $this->id,
                'code' => $code
            ]);

            Mail::to($this->email)->send(new VertifyEmail($this, $code));
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => $th->__toString()
            ]);
        }
    }

    public function sendPasswordResetEmail()
    {
        try {
            $code = Random::generate(6, '0-9');

            PasswordReset::create([
                'email' => $this->email,
                'code' => $code,
            ]);

            Mail::to($this->email)->send(new PasswordResetEmail($this, $code));

            return [
                'status' => true,
                'message' => 'Recovery email was sent'
            ];
        } catch (\Throwable $th) {

            return [
                'status' => false,
                'message' => $th->__toString()
            ];
        }
    }

    /*
     * Relationship
     */

    // Friendship
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followings', 'following_id', 'follower_id');
    }

    // Relationship with followings
    public function followings()
    {
        return $this->belongsToMany(User::class, 'followings', 'follower_id', 'following_id');
    }

    public function friends()
    {
        return $this->belongsToMany(User::class, 'followings', 'follower_id', 'following_id')
        ->whereHas('followings', function ($query) {
            $query->where('following_id', $this->id);
        });
    }

    // post
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // verification code
    public function verification_codes()
    {
        return $this->hasMany(Verification_Code::class);
    }

    public function bannedUser()
    {
        return $this->hasOne(BannedUser::class, 'user_id');
    }

    // Relationship with WarningUser model
    public function warning()
    {
        return $this->hasMany(WarningUser::class, 'user_id');
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

}
