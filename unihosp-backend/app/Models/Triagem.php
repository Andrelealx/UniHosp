<?php

namespace App\Models;

use App\Models\Concerns\HasUuidPrimary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Triagem extends Model
{
    use HasFactory, HasUuidPrimary, SoftDeletes;

    protected $table = 'triagens';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'senha_id',
        'paciente_id',
        'profissional_id',
        'pressao_arterial',
        'temperatura',
        'saturacao',
        'frequencia_cardiaca',
        'peso',
        'altura',
        'glicemia',
        'classificacao_risco',
        'observacoes',
        'encaminhar_fila_id',
        'iniciado_em',
        'finalizado_em',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'temperatura' => 'decimal:1',
            'peso' => 'decimal:2',
            'altura' => 'decimal:2',
            'glicemia' => 'decimal:2',
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
    public function profissional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'profissional_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Fila, $this>
     */
    public function filaEncaminhamento(): BelongsTo
    {
        return $this->belongsTo(Fila::class, 'encaminhar_fila_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Atendimento, $this>
     */
    public function atendimento(): HasOne
    {
        return $this->hasOne(Atendimento::class);
    }
}
