<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\User; // <-- 1. ESTA LINHA ESTAVA EM FALTA
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // <-- 2. ESTA LINHA ESTAVA EM FALTA

class EmpresaController extends Controller
{
    use AuthorizesRequests; // <-- 3. ESTA LINHA ESTAVA EM FALTA

    /**
     * GET /api/empresas
     * Lista todas as empresas.
     */
    public function index(Request $request)
    {
        // Pega apenas as empresas do usuário logado (Avaliador) ou todas (Admin)
        $query = Empresa::orderBy('razaoSocial');

        if ($request->user()->tipo === 'Avaliador') {
            $query->where('user_id', $request->user()->id);
        }

        // Adiciona o ->with('gestores') para carregar os usuários vinculados
        return $query->with('gestores')->get();
    }

    /**
     * POST /api/empresas
     * Cria uma nova empresa.
     */
    public function store(Request $request)
    {
        $dadosValidados = $request->validate([
            'razaoSocial' => 'required|string|max:255',
            'cnpj' => 'required|string|max:20|unique:empresas',
            'setor' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:2',
        ]);

        // Esta linha agora vai funcionar
        $this->authorize('create', Empresa::class);

        $dadosValidados['user_id'] = auth()->id();

        $empresa = Empresa::create($dadosValidados);

        return response()->json($empresa, 201);
    }

    /**
     * GET /api/empresas/{empresa}
     */
    public function show(Empresa $empresa)
    {
        return $empresa;
    }

    /**
     * PUT /api/empresas/{empresa}
     */
    public function update(Request $request, Empresa $empresa)
    {
        $dadosValidados = $request->validate([
            'razaoSocial' => 'required|string|max:255',
            'cnpj' => 'required|string|max:20|unique:empresas,cnpj,' . $empresa->id,
            'setor' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:2',
        ]);

        $this->authorize('update', $empresa);

        $empresa->update($dadosValidados);
        return response()->json($empresa);
    }

    /**
     * DELETE /api/empresas/{empresa}
     */
    public function destroy(Empresa $empresa)
    {
        $this->authorize('delete', $empresa);

        if ($empresa->diagnosticos()->count() > 0) {
            return response()->json([
                'message' => 'Não é possível excluir: Esta empresa já possui diagnósticos vinculados.'
            ], 409); // 409 Conflict
        }

        $empresa->delete();
        return response()->noContent();
    }

    /**
     * GET /api/gestores-disponiveis
     * Lista Gestores que não estão vinculados a nenhuma empresa.
     */
    public function getGestoresDisponiveis()
    {
        return User::where('tipo', 'Gestor Empresarial')
                    ->whereNull('empresa_id')
                    ->orderBy('name')
                    ->get(['id', 'name', 'email']);
    }

    /**
     * POST /api/empresas/{empresa}/vincular-gestor
     * Vincula um Gestor disponível a uma Empresa.
     */
    public function vincularGestor(Request $request, Empresa $empresa)
    {
        $this->authorize('update', $empresa);

        $dadosValidados = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $gestor = User::find($dadosValidados['user_id']);

        if ($gestor->tipo !== 'Gestor Empresarial' || $gestor->empresa_id !== null) {
            return response()->json([
                'message' => 'Este usuário não é um Gestor ou já está vinculado a outra empresa.'
            ], 409); // 409 Conflict
        }

        $gestor->empresa_id = $empresa->id;
        $gestor->save();

        return response()->json($gestor, 200);
    }
}
