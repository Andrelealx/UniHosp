<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChamadaResource;
use App\Models\Chamada;
use App\Models\Senha;
use App\Services\SenhaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChamadaController extends Controller
{
    public function __construct(
        protected SenhaService $senhaService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $chamadas = Chamada::query()
            ->with(['senha.paciente', 'usuario', 'setor', 'sala'])
            ->when($request->filled('setor_id'), fn ($q) => $q->where('setor_id', $request->string('setor_id')->toString()))
            ->latest('chamado_em')
            ->paginate((int) $request->integer('per_page', 30));

        return response()->json([
            'data' => ChamadaResource::collection($chamadas->items()),
            'meta' => [
                'current_page' => $chamadas->currentPage(),
                'per_page' => $chamadas->perPage(),
                'total' => $chamadas->total(),
                'last_page' => $chamadas->lastPage(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $dados = $request->validate([
            'senha_id' => ['required', 'uuid', 'exists:senhas,id'],
            'sala_id' => ['nullable', 'uuid', 'exists:salas,id'],
            'painel_id' => ['nullable', 'uuid', 'exists:paineis,id'],
            'mensagem' => ['nullable', 'string'],
        ]);

        $senha = Senha::query()->findOrFail($dados['senha_id']);
        $senha = $this->senhaService->rechamar($senha, $request->user(), $dados);

        return response()->json([
            'data' => new ChamadaResource(
                $senha->chamadas()->with(['senha.paciente', 'usuario', 'setor', 'sala'])->latest('chamado_em')->first(),
            ),
        ], 201);
    }

    public function repeat(Chamada $chamada, Request $request): JsonResponse
    {
        $dados = [
            'sala_id' => $chamada->sala_id,
            'painel_id' => $chamada->painel_id,
            'mensagem' => $chamada->mensagem,
        ];

        $senha = $this->senhaService->rechamar($chamada->senha, $request->user(), $dados);
        $novaChamada = $senha->chamadas()->with(['senha.paciente', 'usuario', 'setor', 'sala'])->latest('chamado_em')->first();

        return response()->json([
            'data' => new ChamadaResource($novaChamada),
        ]);
    }
}
