<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** A new login is an active administrator unless it is given something else. */
    protected $attributes = ['role' => 'admin', 'is_active' => true];

    /** What each role is called. */
    public const ROLES = ['admin' => 'Administrator', 'finance' => 'Finance', 'sales' => 'Sales', 'content' => 'Content editor'];

    /**
     * The parts of the admin panel each role can open. Administrators can open everything, including staff logins.
     * Areas: dashboard, contacts, enquiries, enrolments, payments, content, staff.
     */
    public const AREAS = [
        'finance' => ['dashboard', 'contacts', 'enrolments', 'payments'],
        'sales' => ['dashboard', 'contacts', 'enquiries', 'enrolments'],
        'content' => ['content'],
    ];

    public function canAccess(string $area): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $role = $this->role ?: 'admin';

        return $role === 'admin' || in_array($area, self::AREAS[$role] ?? [], true);
    }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role ?: 'admin'] ?? ucfirst((string) $this->role);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}
