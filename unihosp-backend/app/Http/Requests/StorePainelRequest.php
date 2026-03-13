<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePainelRequest extends FormRequest
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
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:paineis,slug'],
            'tipo' => ['required', 'in:recepcao,triagem,medico'],
            'setor_id' => ['nullable', 'uuid', 'exists:setores,id'],
            'mensagem_institucional' => ['nullable', 'string'],
            'forma_exibicao_paciente' => ['required', 'in:senha,senha_iniciais,senha_primeiro_nome'],
            'logo_url' => ['nullable', 'url'],
            'ativo' => ['nullable', 'boolean'],
        ];
    }
}
