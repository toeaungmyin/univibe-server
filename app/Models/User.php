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
        'email_verified',
        'email_verified_at',
        'email_verification_code'
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

    public function sendVertifyEmail()
    {
        try {
            $code = Random::generate(6, '0-9');

            $this->update([
                'email_verification_code' => $code
            ]);

            Mail::to($this->email)->send(new VertifyEmail($this));

            return $code;
        } catch (\Throwable $th) {
            $this->delete();
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
}
