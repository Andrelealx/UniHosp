<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConvenioResource;
use App\Http\Resources\FilaResource;
use App\Http\Resources\PainelResource;
use App\Http\Resources\SalaResource;
use App\Http\Resources\SetorResource;
use App\Models\Convenio;
use App\Models\Fila;
use App\Models\Painel;
use App\Models\Sala;
use App\Models\Setor;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

class LookupController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'convenios' => ConvenioResource::collection(Convenio::query()->where('ativo', true)->orderBy('nome')->get()),
            'setores' => SetorResource::collection(Setor::query()->where('ativo', true)->orderBy('nome')->get()),
            'salas' => SalaResource::collection(Sala::query()->where('ativo', true)->with('setor')->orderBy('nome')->get()),
            'filas' => FilaResource::collection(Fila::query()->where('ativo', true)->with('setor')->orderBy('ordem')->get()),
            'paineis' => PainelResource::collection(Painel::query()->where('ativo', true)->with('setor')->orderBy('nome')->get()),
            'roles' => Role::query()->pluck('name'),
            'tipos_atendimento' => ['consulta', 'retorno', 'exame', 'urgencia', 'triagem'],
            'prioridades' => ['normal', 'prioritario', 'urgente'],
            'status_senha' => ['aguardando', 'chamado', 'em_atendimento', 'ausente', 'finalizado', 'cancelado', 'encaminhado'],
        ]);
    }
}
