<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'id' => 'integer',
        ];
    }
    
    /**
     * Get all users with predefined data
     * 
     * @return array
     */
    public static function all($columns = ['*']): array
    {
        // Static user data as provided in the example
        return [
            [
                'id' => 1,
                'email' => 'sergey8osokin@gmail.com',
                'firstname' => 'Sergey',
                'lastname' => 'Osokin',
            ],
            [
                'id' => 2,
                'email' => 'galina8treneva@gmail.com',
                'firstname' => 'Galina',
                'lastname' => 'Treneva',
            ],
            [
                'id' => 3,
                'email' => 'amerkel@germany.de',
                'firstname' => 'Alina',
                'lastname' => 'Merkel',
            ],
            [
                'id' => 5,
                'email' => 'john.doe@usa.us',
                'firstname' => 'John',
                'lastname' => 'Doe',
            ],
        ];
    }
}
