<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'role_id',
        'status',
        'ups_user_uuid',
        'employee_code',
        'phone',
        'designation',
        'role_name',
        'ups_user_id'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // applicant tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'applicant_id');
    }

    // developer assigned tickets
    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'developer_id');
    }

    // team lead tickets
    public function teamLeadTickets()
    {
        return $this->hasMany(Ticket::class, 'team_lead_id');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'category_team_lead',
            'team_lead_id',
            'category_id'
        );
    }

    public function developers()
    {
        return $this->belongsToMany(
            User::class,
            'team_lead_developer',
            'team_lead_id',
            'developer_id'
        );
    }
}
