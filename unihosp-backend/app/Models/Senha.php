<?php

namespace App\Models;

use App\Models\Concerns\HasUuidPrimary;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Senha extends Model
{
    use HasFactory, HasUuidPrimary, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'codigo',
        'numero_sequencial',
        'data_referencia',
        'tipo_atendimento',
        'prioridade',
        'paciente_id',
        'fila_id',
        'setor_id',
        'sala_id',
        'encaminhada_para_fila_id',
        'status',
        'observacoes_iniciais',
        'emitida_por_user_id',
        'chamada_por_user_id',
        'finalizada_por_user_id',
        'horario_emissao',
        'horario_chamada',
        'horario_finalizacao',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_referencia' => 'date',
            'horario_emissao' => 'datetime',
            'horario_chamada' => 'datetime',
            'horario_finalizacao' => 'datetime',
        ];
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\Senha>  $query
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\Senha>
     */
    public function scopeAtivas(Builder $query): Builder
    {
        return $query->whereIn('status', ['aguardando', 'chamado', 'em_atendimento', 'encaminhado']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Paciente, $this>
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Fila, $this>
     */
    public function fila(): BelongsTo
    {
        return $this->belongsTo(Fila::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Setor, $this>
     */
    public function setor(): BelongsTo
    {
        return $this->belongsTo(Setor::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Sala, $this>
     */
    public function sala(): BelongsTo
    {
        return $this->belongsTo(Sala::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Fila, $this>
     */
    public function filaEncaminhada(): BelongsTo
    {
        return $this->belongsTo(Fila::class, 'encaminhada_para_fila_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, $this>
     */
    public function emitidaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'emitida_por_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, $this>
     */
    public function chamadaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'chamada_por_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, $this>
     */
    public function finalizadaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalizada_por_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Chamada, $this>
     */
    public function chamadas(): HasMany
    {
        return $this->hasMany(Chamada::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Triagem, $this>
     */
    public function triagem(): HasOne
    {
        return $this->hasOne(Triagem::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Atendimento, $this>
     */
    public function atendimentos(): HasMany
    {
        return $this->hasMany(Atendimento::class);
    }
}
