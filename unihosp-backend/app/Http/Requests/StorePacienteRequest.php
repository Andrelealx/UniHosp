<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePacienteRequest extends FormRequest
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
            'nome' => ['required', 'string', 'max:255'],
            'nome_social' => ['nullable', 'string', 'max:255'],
            'data_nascimento' => ['nullable', 'date'],
            'sexo' => ['nullable', 'in:M,F,O'],
            'nome_mae' => ['nullable', 'string', 'max:255'],
            'cpf' => ['nullable', 'string', 'max:14', 'unique:pacientes,cpf'],
            'cns' => ['nullable', 'string', 'max:32', 'unique:pacientes,cns'],
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
