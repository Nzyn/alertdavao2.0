<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname', 
        'contact',
        'email',
        'password',
        'address',
        'latitude',
        'longitude',
        'is_verified',
        'station_id',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'is_verified' => 'boolean',
        'latitude' => 'double',
        'longitude' => 'double',
    ];
    
    public function reports()
    {
        return $this->hasMany(Report::class);
    }
    
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }
    
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
    
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id');
    }
    
    public function verifications()
    {
        return $this->hasMany(Verification::class, 'user_id');
    }
    
    public function adminActions()
    {
        return $this->hasMany(AdminAction::class, 'admin_id');
    }
    
    public function policeStation()
    {
        return $this->belongsTo(PoliceStation::class, 'station_id', 'station_id');
    }

    public function officer()
    {
        return $this->hasOne(PoliceOfficer::class, 'user_id', 'id');
    }
}