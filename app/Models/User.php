<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property-read Collection<int, Role> $roles
 * @property-read Company|null $company
 * @property-read User|null $manager
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
  
protected $fillable = [
    'name',
    'email',
    'password',
    'company_id',
    'manager_id',
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
    ];

public function company(): BelongsTo
{
    return $this->belongsTo(Company::class);
}

public function roles(): BelongsToMany
{
    return $this->belongsToMany(Role::class)
                ->withTimestamps();
}

public function manager(): BelongsTo { return $this->belongsTo(self::class, 'manager_id'); }

}
