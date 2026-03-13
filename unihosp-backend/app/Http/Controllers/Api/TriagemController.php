<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChamarSenhaRequest;
use App\Http\Requests\StoreTriagemRequest;
use App\Http\Resources\SenhaResource;
use App\Http\Resources\TriagemResource;
use App\Models\Fila;
use App\Models\Triagem;
use App\Services\TriagemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TriagemController extends Controller
{
    public function __construct(
        protected TriagemService $triagemService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $triagens = Triagem::query()
            ->with(['paciente', 'profissional', 'senha'])
            ->latest('created_at')
            ->paginate((int) $request->integer('per_page', 20));

        return response()->json([
            'data' => TriagemResource::collection($triagens->items()),
            'meta' => [
                'current_page' => $triagens->currentPage(),
                'per_page' => $triagens->perPage(),
                'total' => $triagens->total(),
                'last_page' => $triagens->lastPage(),
            ],
        ]);
    }

    public function chamarProximo(ChamarSenhaRequest $request, Fila $fila): JsonResponse
    {
        $senha = $this->triagemService->chamarProximo($fila, $request->user(), $request->validated());

        if (! $senha) {
            return response()->json([
                'message' => 'Nenhuma senha aguardando para triagem.',
            ], 404);
        }

        return response()->json([
            'data' => new SenhaResource($senha),
        ]);
    }

    public function store(StoreTriagemRequest $request): JsonResponse
    {
        $triagem = $this->triagemService->registrar($request->validated(), $request->user());

        return response()->json([
            'data' => new TriagemResource($triagem),
        ], 201);
    }

    public function show(Triagem $triagem): JsonResponse
    {
        return response()->json([
            'data' => new TriagemResource($triagem->load(['paciente', 'profissional', 'senha', 'filaEncaminhamento'])),
        ]);
    }
}
