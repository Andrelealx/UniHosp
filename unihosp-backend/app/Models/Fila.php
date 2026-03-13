<?php

namespace App\Models;

use App\Models\Concerns\HasUuidPrimary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fila extends Model
{
    use HasFactory, HasUuidPrimary, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'setor_id',
        'nome',
        'codigo',
        'tipo',
        'ordem',
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Setor, $this>
     */
    public function setor(): BelongsTo
    {
        return $this->belongsTo(Setor::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Senha, $this>
     */
    public function senhas(): HasMany
    {
        return $this->hasMany(Senha::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Senha, $this>
     */
    public function senhasEncaminhadas(): HasMany
    {
        return $this->hasMany(Senha::class, 'encaminhada_para_fila_id');
    }
}
