<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePainelRequest extends FormRequest
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
        $painel = $this->route('painel');
        $painelId = is_object($painel) ? $painel->id : $painel;

        return [
            'nome' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', 'alpha_dash', Rule::unique('paineis', 'slug')->ignore($painelId)],
            'tipo' => ['sometimes', 'in:recepcao,triagem,medico'],
            'setor_id' => ['nullable', 'uuid', 'exists:setores,id'],
            'mensagem_institucional' => ['nullable', 'string'],
            'forma_exibicao_paciente' => ['sometimes', 'in:senha,senha_iniciais,senha_primeiro_nome'],
            'logo_url' => ['nullable', 'url'],
            'ativo' => ['nullable', 'boolean'],
        ];
    }
}
