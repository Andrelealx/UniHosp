<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePacienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, mixed>|string>
     */
    public function rules(): array
    {
        $paciente = $this->route('paciente');
        $pacienteId = is_object($paciente) ? $paciente->id : $paciente;

        return [
            'nome' => ['sometimes', 'string', 'max:255'],
            'nome_social' => ['nullable', 'string', 'max:255'],
            'data_nascimento' => ['nullable', 'date'],
            'sexo' => ['nullable', 'in:M,F,O'],
            'nome_mae' => ['nullable', 'string', 'max:255'],
            'cpf' => ['nullable', 'string', 'max:14', Rule::unique('pacientes', 'cpf')->ignore($pacienteId)],
            'cns' => ['nullable', 'string', 'max:32', Rule::unique('pacientes', 'cns')->ignore($pacienteId)],
            'rg' => ['nullable', 'string', 'max:20'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'telefone_secundario' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'endereco' => ['nullable', 'array'],
            'endereco.cep' => ['nullable', 'string', 'max:20'],
            'endereco.logradouro' => ['nullable', 'string', 'max:255'],
            'endereco.numero' => ['nullable', 'string', 'max:20'],
            'endereco.bairro' => ['nullable', 'string', 'max:120'],
            'endereco.cidade' => ['nullable', 'string', 'max:120'],
            'endereco.estado' => ['nullable', 'string', 'max:2'],
            'convenio_id' => ['nullable', 'uuid', 'exists:convenios,id'],
            'responsavel_nome' => ['nullable', 'string', 'max:255'],
            'responsavel_telefone' => ['nullable', 'string', 'max:20'],
            'alergias' => ['nullable', 'string'],
            'comorbidades' => ['nullable', 'string'],
            'observacoes' => ['nullable', 'string'],
            'ativo' => ['nullable', 'boolean'],
        ];
    }
}
