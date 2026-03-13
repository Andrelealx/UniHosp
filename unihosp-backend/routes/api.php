<?php

use App\Http\Controllers\Api\AtendimentoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChamadaController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FilaController;
use App\Http\Controllers\Api\LookupController;
use App\Http\Controllers\Api\PainelController;
use App\Http\Controllers\Api\PacienteController;
use App\Http\Controllers\Api\ProntuarioController;
use App\Http\Controllers\Api\RecepcaoController;
use App\Http\Controllers\Api\RelatorioController;
use App\Http\Controllers\Api\SenhaController;
use App\Http\Controllers\Api\TriagemController;
use App\Http\Controllers\Api\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
});

Route::get('/paineis/publico/{slug}', [PainelController::class, 'exibir']);

Route::middleware(['auth:sanctum', 'audit'])->group(function (): void {
    Route::prefix('auth')->group(function (): void {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('permission:dashboard.view');
    Route::get('/lookups', [LookupController::class, 'index']);

    Route::apiResource('pacientes', PacienteController::class);

    Route::prefix('recepcao')->group(function (): void {
        Route::get('/buscar-paciente', [RecepcaoController::class, 'buscarPaciente'])->middleware('permission:recepcao.view');
        Route::post('/cadastro-rapido', [RecepcaoController::class, 'cadastroRapido'])->middleware('permission:recepcao.manage');
        Route::get('/ficha/{paciente}', [RecepcaoController::class, 'abrirFicha'])->middleware('permission:recepcao.view');
        Route::post('/emitir-senha', [RecepcaoController::class, 'emitirSenha'])->middleware('permission:recepcao.manage');
    });

    Route::prefix('filas')->group(function (): void {
        Route::get('/', [FilaController::class, 'index'])->middleware('permission:filas.view');
        Route::get('/{fila}', [FilaController::class, 'show'])->middleware('permission:filas.view');
        Route::post('/{fila}/chamar-proximo', [FilaController::class, 'chamarProximo'])->middleware('permission:filas.manage');
    });

    Route::prefix('senhas')->group(function (): void {
        Route::get('/', [SenhaController::class, 'index'])->middleware('permission:senhas.view');
        Route::get('/{senha}', [SenhaController::class, 'show'])->middleware('permission:senhas.view');
        Route::get('/{senha}/historico', [SenhaController::class, 'historico'])->middleware('permission:senhas.view');
        Route::post('/{senha}/rechamar', [SenhaController::class, 'rechamar'])->middleware('permission:senhas.manage');
        Route::post('/{senha}/iniciar-atendimento', [SenhaController::class, 'iniciarAtendimento'])->middleware('permission:senhas.manage');
        Route::post('/{senha}/ausente', [SenhaController::class, 'ausente'])->middleware('permission:senhas.manage');
        Route::post('/{senha}/cancelar', [SenhaController::class, 'cancelar'])->middleware('permission:senhas.manage');
        Route::post('/{senha}/encaminhar', [SenhaController::class, 'encaminhar'])->middleware('permission:senhas.manage');
        Route::post('/{senha}/finalizar', [SenhaController::class, 'finalizar'])->middleware('permission:senhas.manage');
    });

    Route::prefix('chamadas')->group(function (): void {
        Route::get('/', [ChamadaController::class, 'index'])->middleware('permission:chamadas.view');
        Route::post('/', [ChamadaController::class, 'store'])->middleware('permission:chamadas.manage');
        Route::post('/{chamada}/repeat', [ChamadaController::class, 'repeat'])->middleware('permission:chamadas.manage');
    });

    Route::prefix('paineis')->group(function (): void {
        Route::get('/', [PainelController::class, 'index'])->middleware('permission:paineis.view');
        Route::post('/', [PainelController::class, 'store'])->middleware('permission:paineis.manage');
        Route::put('/{painel}', [PainelController::class, 'update'])->middleware('permission:paineis.manage');
        Route::delete('/{painel}', [PainelController::class, 'destroy'])->middleware('permission:paineis.manage');
        Route::post('/{painel}/toggle', [PainelController::class, 'toggle'])->middleware('permission:paineis.manage');
    });

    Route::prefix('triagem')->group(function (): void {
        Route::get('/', [TriagemController::class, 'index'])->middleware('permission:triagem.view');
        Route::post('/', [TriagemController::class, 'store'])->middleware('permission:triagem.manage');
        Route::get('/{triagem}', [TriagemController::class, 'show'])->middleware('permission:triagem.view');
        Route::post('/filas/{fila}/chamar-proximo', [TriagemController::class, 'chamarProximo'])->middleware('permission:triagem.manage');
    });

    Route::prefix('prontuarios')->group(function (): void {
        Route::get('/{prontuario}', [ProntuarioController::class, 'show'])->middleware('permission:prontuarios.view');
        Route::get('/paciente/{paciente}', [ProntuarioController::class, 'porPaciente'])->middleware('permission:prontuarios.view');
    });

    Route::prefix('atendimentos')->group(function (): void {
        Route::get('/', [AtendimentoController::class, 'index'])->middleware('permission:atendimentos.view');
        Route::post('/', [AtendimentoController::class, 'store'])->middleware('permission:atendimentos.manage');
        Route::get('/{atendimento}', [AtendimentoController::class, 'show'])->middleware('permission:atendimentos.view');
        Route::post('/filas/{fila}/chamar-proximo', [AtendimentoController::class, 'chamarProximo'])->middleware('permission:atendimentos.manage');
    });

    Route::get('/relatorios', [RelatorioController::class, 'index'])->middleware('permission:relatorios.view');

    Route::prefix('usuarios')->group(function (): void {
        Route::get('/roles', [UsuarioController::class, 'roles'])->middleware('permission:users.manage');
        Route::get('/', [UsuarioController::class, 'index'])->middleware('permission:users.manage');
        Route::post('/', [UsuarioController::class, 'store'])->middleware('permission:users.manage');
        Route::get('/{user}', [UsuarioController::class, 'show'])->middleware('permission:users.manage');
        Route::put('/{user}', [UsuarioController::class, 'update'])->middleware('permission:users.manage');
        Route::delete('/{user}', [UsuarioController::class, 'destroy'])->middleware('permission:users.manage');
    });
});
