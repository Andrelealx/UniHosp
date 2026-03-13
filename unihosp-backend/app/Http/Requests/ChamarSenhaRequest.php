<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChamarSenhaRequest extends FormRequest
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
            'sala_id' => ['nullable', 'uuid', 'exists:salas,id'],
            'painel_id' => ['nullable', 'uuid', 'exists:paineis,id'],
            'tipo' => ['nullable', 'in:chamada,rechamada,sistema'],
            'mensagem' => ['nullable', 'string'],
        ];
    }
}
