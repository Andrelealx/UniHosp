<?php

namespace App\Models;

use App\Models\Concerns\HasUuidPrimary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prontuario extends Model
{
    use HasFactory, HasUuidPrimary;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'paciente_id',
        'resumo_clinico',
        'alergias',
        'comorbidades',
        'observacoes',
        'ultimo_atendimento_em',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ultimo_atendimento_em' => 'datetime',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Paciente, $this>
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\EvolucaoMedica, $this>
     */
    public function evolucoes(): HasMany
    {
        return $this->hasMany(EvolucaoMedica::class);
    }
}
