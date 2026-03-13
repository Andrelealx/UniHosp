<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmitirSenhaRequest;
use App\Http\Resources\PacienteResource;
use App\Http\Resources\SenhaResource;
use App\Models\Paciente;
use App\Services\RecepcaoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecepcaoController extends Controller
{
    public function __construct(
        protected RecepcaoService $recepcaoService,
    ) {}

    public function buscarPaciente(Request $request): JsonResponse
    {
        $query = $request->string('q')->toString();

        $pacientes = Paciente::query()
            ->with('convenio')
            ->search($query)
            ->limit(20)
            ->get();

        return response()->json([
            'data' => PacienteResource::collection($pacientes),
        ]);
    }

    public function cadastroRapido(Request $request): JsonResponse
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'cpf' => ['nullable', 'string', 'max:14', 'unique:pacientes,cpf'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'data_nascimento' => ['nullable', 'date'],
            'sexo' => ['nullable', 'in:M,F,O'],
        ]);

        $paciente = Paciente::query()->create(array_merge($dados, [
            'prontuario_codigo' => sprintf('PRT-%s-%04d', now()->format('Ymd'), random_int(1, 9999)),
        ]));

        return response()->json([
            'data' => new PacienteResource($paciente),
        ], 201);
    }

    public function abrirFicha(Paciente $paciente): JsonResponse
    {
        $dados = $this->recepcaoService->abrirFichaPaciente($paciente);

        return response()->json([
            'paciente' => new PacienteResource($dados['paciente']),
            'senhas_recentes' => SenhaResource::collection($dados['senhas_recentes']),
        ]);
    }

    public function emitirSenha(EmitirSenhaRequest $request): JsonResponse
    {
        $senha = $this->recepcaoService->emitirSenha($request->validated(), $request->user());

        return response()->json([
            'data' => new SenhaResource($senha),
            'comprovante' => [
                'senha' => $senha->codigo,
                'fila' => $senha->fila?->nome,
                'setor' => $senha->setor?->nome,
                'emitido_em' => optional($senha->horario_emissao)?->format('d/m/Y H:i'),
            ],
        ], 201);
    }
}
