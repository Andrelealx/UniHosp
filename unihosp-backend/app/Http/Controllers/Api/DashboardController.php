<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DashboardFilterRequest;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService,
    ) {}

    public function index(DashboardFilterRequest $request): JsonResponse
    {
        $inicio = $request->validated('inicio') ? Carbon::parse($request->validated('inicio')) : null;
        $fim = $request->validated('fim') ? Carbon::parse($request->validated('fim')) : null;

        return response()->json(
            $this->dashboardService->resumo($inicio, $fim),
        );
    }
}
