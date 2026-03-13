<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePacienteRequest;
use App\Http\Requests\UpdatePacienteRequest;
use App\Http\Resources\PacienteResource;
use App\Models\Paciente;
use App\Models\Prontuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Paciente::class);

        $pacientes = Paciente::query()
            ->with('convenio')
            ->search($request->string('q')->toString())
            ->orderBy('nome')
            ->paginate((int) $request->integer('per_page', 15));

        return response()->json([
            'data' => PacienteResource::collection($pacientes->items()),
            'meta' => [
                'current_page' => $pacientes->currentPage(),
                'per_page' => $pacientes->perPage(),
                'total' => $pacientes->total(),
                'last_page' => $pacientes->lastPage(),
            ],
        ]);
    }

    public function store(StorePacienteRequest $request): JsonResponse
    {
        $this->authorize('create', Paciente::class);

        $paciente = Paciente::query()->create(
            array_merge(
                $request->validated(),
                ['prontuario_codigo' => $this->gerarCodigoProntuario()],
            ),
        );

        Prontuario::query()->create([
            'paciente_id' => $paciente->id,
            'alergias' => $paciente->alergias,
            'comorbidades' => $paciente->comorbidades,
            'observacoes' => $paciente->observacoes,
        ]);

        return response()->json([
            'data' => new PacienteResource($paciente->load('convenio')),
        ], 201);
    }

    public function show(Paciente $paciente): JsonResponse
    {
        $this->authorize('view', $paciente);

        return response()->json([
            'data' => new PacienteResource($paciente->load('convenio')),
        ]);
    }

    public function update(UpdatePacienteRequest $request, Paciente $paciente): JsonResponse
    {
        $this->authorize('update', $paciente);

        $paciente->update($request->validated());

        return response()->json([
            'data' => new PacienteResource($paciente->fresh()->load('convenio')),
        ]);
    }

    public function destroy(Paciente $paciente): JsonResponse
    {
        $this->authorize('delete', $paciente);
        $paciente->delete();

        return response()->json([
            'message' => 'Paciente removido com sucesso.',
        ]);
    }

    protected function gerarCodigoProntuario(): string
    {
        $prefixo = now()->format('Ymd');
        $sequencial = (int) Paciente::query()
            ->whereDate('created_at', now()->toDateString())
            ->count() + 1;

        return sprintf('PRT-%s-%04d', $prefixo, $sequencial);
    }
}
