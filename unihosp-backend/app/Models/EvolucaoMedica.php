<?php

namespace App\Models;

use App\Models\Concerns\HasUuidPrimary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvolucaoMedica extends Model
{
    use HasFactory, HasUuidPrimary;

    protected $table = 'evolucoes_medicas';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'prontuario_id',
        'atendimento_id',
        'medico_id',
        'descricao',
        'cid_codigo',
        'data_registro',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_registro' => 'datetime',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Prontuario, $this>
     */
    public function prontuario(): BelongsTo
    {
        return $this->belongsTo(Prontuario::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Atendimento, $this>
     */
    public function atendimento(): BelongsTo
    {
        return $this->belongsTo(Atendimento::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, $this>
     */
    public function medico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'medico_id');
    }
}
