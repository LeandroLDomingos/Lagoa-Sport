<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Enums\UserStatus;

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
        'name',
        'email',
        'password',
        'rg',
        'cpf',
        'birthdate',
        'zip_code',
        'address',
        'neighborhood',
        'number',
        'complement',
        'city',
        'approved_at',
        'state',
        'country',
        'status',
        'term'
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
        ];
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }


    protected $keyType = 'string';  // Defina o tipo da chave como 'string'
    public $incrementing = false;  // Impede o Laravel de tentar incrementar a chave (porque é UUID)

    protected static function boot()
    {
        parent::boot();

        // Gerar UUID antes de criar o usuário
        static::creating(function ($user) {
            if (empty($user->id)) {
                $user->id = (string) Str::uuid();  // Gerando UUID
            }
        });
    }


    public function getStatusLabelAttribute(): string
    {
        try {
            return UserStatus::from($this->status)->label();
        } catch (\ValueError) {
            return 'Desconhecido';
        }
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function hasPermission(string $permission): bool
    {
        // Se tiver a role 'admin', libera tudo automaticamente
        if ($this->hasRole('admin')) {
            return true;
        }
        // Verifica permissões diretas
        $hasDirectPermission = $this->permissions()->where('name', $permission)->exists();

        // Verifica permissões herdadas de roles
        $hasPermissionViaRole = $this->roles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('name', $permission);
            })->exists();

        return $hasDirectPermission || $hasPermissionViaRole;
    }

}
