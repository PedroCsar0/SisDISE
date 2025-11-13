<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DiagnosticoController;
use App\Http\Controllers\Api\EmpresaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rota de Login PÚBLICA (para o Vue usar)
Route::post('/login', [AuthController::class, 'login']);

// --- ROTAS PROTEGIDAS ---
// Todas as rotas aqui dentro exigem um Token de API válido
Route::middleware('auth:sanctum')->group(function () {

    // Grupo de rotas do Administrador
    Route::prefix('admin')->middleware('can:ser-admin')->group(function () {
        // GET /api/admin/parametros
        Route::get('/parametros', [AdminController::class, 'getParametros']);

        // PUT /api/admin/parametros/{itemParametro}
        Route::put('/parametros/{itemParametro}', [AdminController::class, 'updateParametro']);

        // PUT /api/admin/grupos/{grupoParametro}
        Route::put('/grupos/{grupoParametro}', [AdminController::class, 'updateGrupo']);
    });

    // --- Rotas do Avaliador ---

    // CRUD de Empresas
    // (GET, POST, PUT, DELETE para /api/empresas)
    Route::apiResource('empresas', EmpresaController::class);

    // Rotas de Diagnóstico
    Route::get('/questionario', [DiagnosticoController::class, 'getQuestionario']);
    Route::post('/diagnosticos', [DiagnosticoController::class, 'store']);
    Route::get('/diagnosticos/{diagnostico}', [DiagnosticoController::class, 'show']);
    Route::get('/diagnosticos', [DiagnosticoController::class, 'index']);
    Route::get('/relatorio/{diagnostico}/pdf', [DiagnosticoController::class, 'downloadPDF']);
    Route::get('/diagnosticos/{diagnostico}/pges', [DiagnosticoController::class, 'gerarPGES']);

    // Rota padrão para buscar o usuário logado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
