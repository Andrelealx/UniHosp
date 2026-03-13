<?php

namespace App\Models;

use App\Models\Concerns\HasUuidPrimary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setor extends Model
{
    use HasFactory, HasUuidPrimary, SoftDeletes;

    protected $table = 'setores';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'nome',
        'codigo',
        'tipo',
        'descricao',
        'ativo',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Sala, $this>
     */
    public function salas(): HasMany
    {
        return $this->hasMany(Sala::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Fila, $this>
     */
    public function filas(): HasMany
    {
        return $this->hasMany(Fila::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Senha, $this>
     */
    public function senhas(): HasMany
    {
        return $this->hasMany(Senha::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Chamada, $this>
     */
    public function chamadas(): HasMany
    {
        return $this->hasMany(Chamada::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Painel, $this>
     */
    public function paineis(): HasMany
    {
        return $this->hasMany(Painel::class);
    }
}
