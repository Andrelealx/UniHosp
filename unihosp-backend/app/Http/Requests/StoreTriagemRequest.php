<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTriagemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'senha_id' => ['required', 'uuid', 'exists:senhas,id'],
            'paciente_id' => ['required', 'uuid', 'exists:pacientes,id'],
            'pressao_arterial' => ['nullable', 'string', 'max:20'],
            'temperatura' => ['nullable', 'numeric', 'between:30,45'],
            'saturacao' => ['nullable', 'integer', 'between:0,100'],
            'frequencia_cardiaca' => ['nullable', 'integer', 'between:20,250'],
            'peso' => ['nullable', 'numeric', 'between:1,500'],
            'altura' => ['nullable', 'numeric', 'between:0.3,2.5'],
            'glicemia' => ['nullable', 'numeric', 'between:10,900'],
            'classificacao_risco' => ['nullable', 'in:baixo,medio,alto,critico'],
            'observacoes' => ['nullable', 'string'],
            'encaminhar_fila_id' => ['nullable', 'uuid', 'exists:filas,id'],
        ];
    }
}
