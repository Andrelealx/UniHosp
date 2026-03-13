<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProntuarioResource;
use App\Models\Paciente;
use App\Models\Prontuario;
use App\Services\ProntuarioService;
use Illuminate\Http\JsonResponse;

class ProntuarioController extends Controller
{
    public function __construct(
        protected ProntuarioService $prontuarioService,
    ) {}

    public function show(Prontuario $prontuario): JsonResponse
    {
        return response()->json([
            'data' => new ProntuarioResource($prontuario->load(['paciente', 'evolucoes'])),
        ]);
    }

    public function porPaciente(Paciente $paciente): JsonResponse
    {
        $dados = $this->prontuarioService->obterPorPaciente($paciente);

        return response()->json([
            'data' => new ProntuarioResource($dados['prontuario']),
            'timeline' => $dados['timeline'],
        ]);
    }
}
