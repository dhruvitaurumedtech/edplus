<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';
    protected $fillable = [
        'unique_id', 'firstname', 'lastname', 'email', 'email_verified_at','country_code','country_code_name', 'mobile', 'otp_num','status', 'password', 'image', 'role_type', 'country', 'state', 'city', 'pincode', 'address', 'dob', 'school_name', 'area', 'employee_type', 'qualification',
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
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
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


    // get User Profile image
    public function getImageAttribute($value)
    {
        if (!empty($value)) {
            return asset($value);
        } else {
            return asset('profile/no-image.png');
        }
    }

    public function studentsDetails()
    {
        return $this->hasMany(Student_detail::class, 'user_id');
    }
}
