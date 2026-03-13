<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChamarSenhaRequest;
use App\Http\Resources\FilaResource;
use App\Http\Resources\SenhaResource;
use App\Models\Fila;
use App\Services\SenhaService;
use Illuminate\Http\JsonResponse;

class FilaController extends Controller
{
    public function __construct(
        protected SenhaService $senhaService,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->senhaService->snapshotFilas());
    }

    public function show(Fila $fila): JsonResponse
    {
        $fila->load('setor');

        $senhas = $fila->senhas()
            ->with(['paciente', 'setor', 'sala'])
            ->whereIn('status', ['aguardando', 'chamado', 'em_atendimento', 'encaminhado'])
            ->orderByRaw("CASE prioridade WHEN 'urgente' THEN 1 WHEN 'prioritario' THEN 2 ELSE 3 END")
            ->orderBy('horario_emissao')
            ->get();

        return response()->json([
            'fila' => new FilaResource($fila),
            'senhas' => SenhaResource::collection($senhas),
        ]);
    }

    public function chamarProximo(ChamarSenhaRequest $request, Fila $fila): JsonResponse
    {
        $senha = $this->senhaService->chamarProximo($fila, $request->user(), $request->validated());

        if (! $senha) {
            return response()->json([
                'message' => 'Nenhuma senha aguardando nesta fila.',
            ], 404);
        }

        return response()->json([
            'data' => new SenhaResource($senha),
        ]);
    }
}
