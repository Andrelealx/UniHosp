<?php

namespace App\Models;

use App\Models\Concerns\HasUuidPrimary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Atendimento extends Model
{
    use HasFactory, HasUuidPrimary, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'senha_id',
        'paciente_id',
        'medico_id',
        'triagem_id',
        'queixa_principal',
        'hipotese_diagnostica',
        'cid_codigo',
        'conduta',
        'prescricao_resumo',
        'status',
        'iniciado_em',
        'finalizado_em',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'iniciado_em' => 'datetime',
            'finalizado_em' => 'datetime',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Senha, $this>
     */
    public function senha(): BelongsTo
    {
        return $this->belongsTo(Senha::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Paciente, $this>
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, $this>
     */
    public function medico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'medico_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Triagem, $this>
     */
    public function triagem(): BelongsTo
    {
        return $this->belongsTo(Triagem::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Prescricao, $this>
     */
    public function prescricoes(): HasMany
    {
        return $this->hasMany(Prescricao::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\EvolucaoMedica, $this>
     */
    public function evolucoes(): HasMany
    {
        return $this->hasMany(EvolucaoMedica::class);
    }
}
