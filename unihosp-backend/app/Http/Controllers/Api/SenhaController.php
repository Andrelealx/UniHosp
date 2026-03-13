<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChamarSenhaRequest;
use App\Http\Requests\EncaminharSenhaRequest;
use App\Http\Resources\ChamadaResource;
use App\Http\Resources\SenhaResource;
use App\Models\Fila;
use App\Models\Senha;
use App\Services\SenhaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SenhaController extends Controller
{
    public function __construct(
        protected SenhaService $senhaService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Senha::query()
            ->with(['paciente', 'fila', 'setor', 'sala'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->when($request->filled('fila_id'), fn ($q) => $q->where('fila_id', $request->string('fila_id')->toString()))
            ->when($request->filled('setor_id'), fn ($q) => $q->where('setor_id', $request->string('setor_id')->toString()))
            ->latest('horario_emissao');

        $senhas = $query->paginate((int) $request->integer('per_page', 20));

        return response()->json([
            'data' => SenhaResource::collection($senhas->items()),
            'meta' => [
                'current_page' => $senhas->currentPage(),
                'per_page' => $senhas->perPage(),
                'total' => $senhas->total(),
                'last_page' => $senhas->lastPage(),
            ],
        ]);
    }

    public function show(Senha $senha): JsonResponse
    {
        return response()->json([
            'data' => new SenhaResource($senha->load(['paciente', 'fila', 'setor', 'sala', 'chamadas'])),
        ]);
    }

    public function rechamar(ChamarSenhaRequest $request, Senha $senha): JsonResponse
    {
        $senha = $this->senhaService->rechamar($senha, $request->user(), $request->validated());

        return response()->json([
            'data' => new SenhaResource($senha),
        ]);
    }

    public function iniciarAtendimento(Request $request, Senha $senha): JsonResponse
    {
        $senha = $this->senhaService->iniciarAtendimento($senha, $request->user());

        return response()->json([
            'data' => new SenhaResource($senha),
        ]);
    }

    public function ausente(Request $request, Senha $senha): JsonResponse
    {
        $senha = $this->senhaService->marcarAusente($senha, $request->user());

        return response()->json([
            'data' => new SenhaResource($senha),
        ]);
    }

    public function cancelar(Request $request, Senha $senha): JsonResponse
    {
        $senha = $this->senhaService->cancelar($senha, $request->user());

        return response()->json([
            'data' => new SenhaResource($senha),
        ]);
    }

    public function encaminhar(EncaminharSenhaRequest $request, Senha $senha): JsonResponse
    {
        $fila = Fila::query()->findOrFail($request->validated('fila_id'));
        $senha = $this->senhaService->encaminhar($senha, $fila);

        return response()->json([
            'data' => new SenhaResource($senha),
        ]);
    }

    public function finalizar(Request $request, Senha $senha): JsonResponse
    {
        $senha = $this->senhaService->finalizar($senha, $request->user());

        return response()->json([
            'data' => new SenhaResource($senha),
        ]);
    }

    public function historico(Senha $senha): JsonResponse
    {
        $chamadas = $senha->chamadas()
            ->with(['usuario', 'setor', 'sala'])
            ->latest('chamado_em')
            ->get();

        return response()->json([
            'data' => ChamadaResource::collection($chamadas),
        ]);
    }
}
