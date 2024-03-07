<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table= 'users';
    protected $fillable = [
        'firstname','lastname', 'email','mobile', 'otp_num', 'password', 'image', 'role_type','address','dob'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function getJWTIdentifier() {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [
            // 'email'=>$this->email,
            // 'name'=>$this->name
          ];
    }    
    public function canButton($permission, $menu_name)
    {
        // Check if the user has the specified permission for the given menu ID
        $menu = Menu::where('menu_name', $menu_name)->first();
        $existingPermission = permission::where('role_id', $this->role_type)
            ->where('menu_id', $menu->id)
            ->first();

        return $existingPermission && $existingPermission->$permission == 1;
    }
}
