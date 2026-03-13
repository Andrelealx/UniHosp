<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmitirSenhaRequest extends FormRequest
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
            'paciente_id' => ['required', 'uuid', 'exists:pacientes,id'],
            'fila_id' => ['required', 'uuid', 'exists:filas,id'],
            'setor_id' => ['required', 'uuid', 'exists:setores,id'],
            'tipo_atendimento' => ['required', 'in:consulta,retorno,exame,urgencia,triagem'],
            'prioridade' => ['required', 'in:normal,prioritario,urgente'],
            'observacoes_iniciais' => ['nullable', 'string'],
            'sala_id' => ['nullable', 'uuid', 'exists:salas,id'],
        ];
    }
}
