<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $appends = [
        'roles_list',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'cpf',
        'phone',
        'password',
        'is_active',
        'last_login_at',
        'last_login_ip',
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
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Senha, $this>
     */
    public function senhasEmitidas(): HasMany
    {
        return $this->hasMany(Senha::class, 'emitida_por_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Senha, $this>
     */
    public function senhasChamadas(): HasMany
    {
        return $this->hasMany(Senha::class, 'chamada_por_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Senha, $this>
     */
    public function senhasFinalizadas(): HasMany
    {
        return $this->hasMany(Senha::class, 'finalizada_por_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Chamada, $this>
     */
    public function chamadas(): HasMany
    {
        return $this->hasMany(Chamada::class, 'usuario_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Triagem, $this>
     */
    public function triagens(): HasMany
    {
        return $this->hasMany(Triagem::class, 'profissional_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Atendimento, $this>
     */
    public function atendimentos(): HasMany
    {
        return $this->hasMany(Atendimento::class, 'medico_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\EvolucaoMedica, $this>
     */
    public function evolucoesMedicas(): HasMany
    {
        return $this->hasMany(EvolucaoMedica::class, 'medico_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Prescricao, $this>
     */
    public function prescricoes(): HasMany
    {
        return $this->hasMany(Prescricao::class, 'medico_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\AuditoriaLog, $this>
     */
    public function auditorias(): HasMany
    {
        return $this->hasMany(AuditoriaLog::class, 'user_id');
    }

    /**
     * @return array<string>
     */
    public function getRolesListAttribute(): array
    {
        return $this->getRoleNames()->toArray();
    }
}
