<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Restore; // <--- Tambahkan baris ini

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLES = [
        'Admin' => 'Admin',
        'Librarian' => 'Pustakawan',
        'Member' => 'Member',
    ];

    public const NUMBER_TYPES = [
        'NIS',
        'NIP',
        'NIK',
    ];

    public const GENDERS = [
        'Man' => 'Laki-laki',
        'Woman' => 'Perempuan',
    ];

    protected $fillable = [
        'name',
        'number',
        'number_type',
        'role',
        'password',
        'address',
        'telephone',
        'gender',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function borrows() {
        return $this->hasMany(Borrow::class);
    }

    /**
     * Get the restores (returns) for the user.
     */
    public function restores() // <--- Tambahkan metode ini
    {
        return $this->hasMany(Restore::class, 'user_id');
        // Pastikan 'user_id' adalah foreign key di tabel 'returns' (yang diwakili oleh model Restore)
        // yang mengacu ke id user.
    }
}