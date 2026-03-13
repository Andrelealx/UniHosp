<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePainelRequest;
use App\Http\Requests\UpdatePainelRequest;
use App\Http\Resources\PainelResource;
use App\Models\Painel;
use App\Services\PainelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PainelController extends Controller
{
    public function __construct(
        protected PainelService $painelService,
    ) {}

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Painel::class);

        $paineis = Painel::query()->with('setor')->orderBy('nome')->get();

        return response()->json([
            'data' => PainelResource::collection($paineis),
        ]);
    }

    public function store(StorePainelRequest $request): JsonResponse
    {
        $this->authorize('create', Painel::class);

        $painel = Painel::query()->create($request->validated());

        return response()->json([
            'data' => new PainelResource($painel->load('setor')),
        ], 201);
    }

    public function update(UpdatePainelRequest $request, Painel $painel): JsonResponse
    {
        $this->authorize('update', $painel);

        $painel->update($request->validated());

        return response()->json([
            'data' => new PainelResource($painel->fresh()->load('setor')),
        ]);
    }

    public function destroy(Painel $painel): JsonResponse
    {
        $this->authorize('delete', $painel);
        $painel->delete();

        return response()->json([
            'message' => 'Painel removido com sucesso.',
        ]);
    }

    public function toggle(Painel $painel, Request $request): JsonResponse
    {
        $this->authorize('update', $painel);

        $painel->update([
            'ativo' => ! $painel->ativo,
        ]);

        return response()->json([
            'data' => new PainelResource($painel->fresh()->load('setor')),
        ]);
    }

    public function exibir(string $slug): JsonResponse
    {
        return response()->json(
            $this->painelService->dadosPorSlug($slug),
        );
    }
}
