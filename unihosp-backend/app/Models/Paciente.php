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

class Paciente extends Model
{
    use HasFactory, HasUuidPrimary, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'prontuario_codigo',
        'nome',
        'nome_social',
        'data_nascimento',
        'sexo',
        'nome_mae',
        'cpf',
        'cns',
        'rg',
        'telefone',
        'telefone_secundario',
        'email',
        'endereco',
        'convenio_id',
        'responsavel_nome',
        'responsavel_telefone',
        'alergias',
        'comorbidades',
        'observacoes',
        'ativo',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_nascimento' => 'date',
            'endereco' => 'array',
            'ativo' => 'boolean',
        ];
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\Paciente>  $query
     * @param  string|null  $term
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\Paciente>
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($term): void {
            $operator = $builder->getConnection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $builder
                ->where('nome', $operator, "%{$term}%")
                ->orWhere('cpf', $operator, "%{$term}%")
                ->orWhere('cns', $operator, "%{$term}%")
                ->orWhere('telefone', $operator, "%{$term}%");
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Convenio, $this>
     */
    public function convenio(): BelongsTo
    {
        return $this->belongsTo(Convenio::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Senha, $this>
     */
    public function senhas(): HasMany
    {
        return $this->hasMany(Senha::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Triagem, $this>
     */
    public function triagens(): HasMany
    {
        return $this->hasMany(Triagem::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Atendimento, $this>
     */
    public function atendimentos(): HasMany
    {
        return $this->hasMany(Atendimento::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Prescricao, $this>
     */
    public function prescricoes(): HasMany
    {
        return $this->hasMany(Prescricao::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Prontuario, $this>
     */
    public function prontuario(): HasOne
    {
        return $this->hasOne(Prontuario::class);
    }
}
