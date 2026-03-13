<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAtendimentoRequest extends FormRequest
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
            'triagem_id' => ['nullable', 'uuid', 'exists:triagens,id'],
            'queixa_principal' => ['nullable', 'string'],
            'hipotese_diagnostica' => ['nullable', 'string'],
            'cid_codigo' => ['nullable', 'string', 'max:20'],
            'conduta' => ['nullable', 'string'],
            'prescricao_resumo' => ['nullable', 'string'],
            'prescricao_texto' => ['nullable', 'string'],
            'orientacoes' => ['nullable', 'string'],
        ];
    }
}
