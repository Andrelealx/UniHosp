<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RelatorioFilterRequest;
use App\Services\RelatorioService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class RelatorioController extends Controller
{
    public function __construct(
        protected RelatorioService $relatorioService,
    ) {}

    public function index(RelatorioFilterRequest $request): JsonResponse
    {
        $dados = $request->validated();

        return response()->json(
            $this->relatorioService->gerar(
                Carbon::parse($dados['inicio']),
                Carbon::parse($dados['fim']),
                $dados['setor_id'] ?? null,
                $dados['fila_id'] ?? null,
            ),
        );
    }
}
