<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DiagnosticoController;
use App\Http\Controllers\Api\EmpresaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PasswordResetController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// === ROTAS PÚBLICAS ===
// (Não precisam de login)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
// --- Rotas de Senha ---
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);

// === ROTAS PROTEGIDAS (Exigem Login) ===
Route::middleware('auth:sanctum')->group(function () {

    // --- Rotas de Admin (Exigem tipo 'Administrador') ---
    Route::prefix('admin')->middleware('can:ser-admin')->group(function () {
        Route::get('/parametros', [AdminController::class, 'getParametros']);
        Route::put('/parametros/{itemParametro}', [AdminController::class, 'updateParametro']);
        Route::put('/grupos/{grupoParametro}', [AdminController::class, 'updateGrupo']);
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::get('/users/{user}', [AdminController::class, 'getUserDetails']);
        Route::put('/users/{user}', [AdminController::class, 'updateUser']);
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser']);
    });

    // --- Rotas de Gestão (Exigem tipo 'Administrador' OU 'Avaliador') ---
    Route::middleware('can:pode-gerir')->group(function () {
        // CRUD de Empresas
        Route::apiResource('empresas', EmpresaController::class);
        // Gestão de Diagnósticos
        Route::get('/questionario', [DiagnosticoController::class, 'getQuestionario']);
        Route::post('/diagnosticos', [DiagnosticoController::class, 'store']);
        // Gestão de Vínculos
        Route::get('/gestores-disponiveis', [EmpresaController::class, 'getGestoresDisponiveis']);
        Route::post('/empresas/{empresa}/vincular-gestor', [EmpresaController::class, 'vincularGestor']);
    });

    // --- Rotas Comuns (Todos os tipos, incluindo 'Gestor Empresarial') ---

    // 1. ROTAS ESPECÍFICAS (Devem vir PRIMEIRO)
    // Downloads e PGES
    Route::get('/relatorio/{diagnostico}/pdf', [DiagnosticoController::class, 'downloadPDF']);
    Route::get('/diagnosticos/{diagnostico}/pges', [DiagnosticoController::class, 'gerarPGES']);
    Route::get('/diagnosticos/{diagnostico}/pges/pdf', [DiagnosticoController::class, 'downloadPGES']);

    // 2. LISTAGEM GERAL
    Route::get('/diagnosticos', [DiagnosticoController::class, 'index']); 

    // 3. ROTA GENÉRICA (Deve ser a ÚLTIMA, pois captura o ID)
    Route::get('/diagnosticos/{diagnostico}', [DiagnosticoController::class, 'show']);
    Route::delete('/diagnosticos/{diagnostico}', [DiagnosticoController::class, 'destroy']);

    // Buscar o próprio utilizador
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rota de Perfil
    Route::put('/profile', [ProfileController::class, 'update']);
});
