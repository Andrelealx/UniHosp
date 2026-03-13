<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChamarSenhaRequest;
use App\Http\Requests\StoreAtendimentoRequest;
use App\Http\Resources\AtendimentoResource;
use App\Http\Resources\SenhaResource;
use App\Models\Atendimento;
use App\Models\Fila;
use App\Services\AtendimentoService;
use App\Services\SenhaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AtendimentoController extends Controller
{
    public function __construct(
        protected AtendimentoService $atendimentoService,
        protected SenhaService $senhaService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $atendimentos = Atendimento::query()
            ->with(['paciente', 'medico', 'triagem'])
            ->latest('created_at')
            ->paginate((int) $request->integer('per_page', 20));

        return response()->json([
            'data' => AtendimentoResource::collection($atendimentos->items()),
            'meta' => [
                'current_page' => $atendimentos->currentPage(),
                'per_page' => $atendimentos->perPage(),
                'total' => $atendimentos->total(),
                'last_page' => $atendimentos->lastPage(),
            ],
        ]);
    }

    public function chamarProximo(ChamarSenhaRequest $request, Fila $fila): JsonResponse
    {
        $senha = $this->senhaService->chamarProximo($fila, $request->user(), $request->validated());

        if (! $senha) {
            return response()->json([
                'message' => 'Nenhuma senha aguardando para atendimento médico.',
            ], 404);
        }

        return response()->json([
            'data' => new SenhaResource($senha),
        ]);
    }

    public function store(StoreAtendimentoRequest $request): JsonResponse
    {
        $atendimento = $this->atendimentoService->registrar($request->validated(), $request->user());

        return response()->json([
            'data' => new AtendimentoResource($atendimento),
        ], 201);
    }

    public function show(Atendimento $atendimento): JsonResponse
    {
        return response()->json([
            'data' => new AtendimentoResource($atendimento->load(['paciente', 'medico', 'triagem', 'prescricoes'])),
        ]);
    }
}
