<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'institution_id', 'department_id', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'submitted_by');
    }

    /** Institution accounts only see their own institution's data. */
    public function isInstitutionUser(): bool
    {
        return $this->hasAnyRole([
            'Institution Admin', 'Institution Data Officer', 'Institution Supervisor',
            'Institution Accounting Officer', 'Institution API Account',
        ]);
    }

    /** Ministry department officers only see their own department's data. */
    public function isMinistryDepartmentUser(): bool
    {
        return $this->hasRole('Ministry Department Data Officer');
    }
}
