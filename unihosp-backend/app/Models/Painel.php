<?php

namespace App\Models;

use App\Models\Concerns\HasUuidPrimary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Painel extends Model
{
    use HasFactory, HasUuidPrimary, SoftDeletes;

    protected $table = 'paineis';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'nome',
        'slug',
        'tipo',
        'setor_id',
        'mensagem_institucional',
        'forma_exibicao_paciente',
        'logo_url',
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Chamada, $this>
     */
    public function chamadas(): HasMany
    {
        return $this->hasMany(Chamada::class);
    }
}
