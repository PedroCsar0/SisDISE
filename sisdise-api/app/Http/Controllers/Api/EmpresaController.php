<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate; // Usaremos para segurança

class EmpresaController extends Controller
{
    /**
     * GET /api/empresas
     * Lista todas as empresas.
     */
    public function index()
    {
        // Retorna todas as empresas, ordenadas por nome
        return Empresa::orderBy('razaoSocial')->get();
    }

    /**
     * POST /api/empresas
     * Cria uma nova empresa.
     */
    public function store(Request $request)
    {
        // Valida os dados recebidos do frontend
        $dadosValidados = $request->validate([
            'razaoSocial' => 'required|string|max:255',
            'cnpj' => 'required|string|max:20|unique:empresas', // Garante CNPJ único
            'setor' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:2',
        ]);

        // Cria a nova empresa no banco
        $empresa = Empresa::create($dadosValidados);

        // Retorna a empresa recém-criada e um status 201 (Created)
        return response()->json($empresa, 201);
    }

    /**
     * GET /api/empresas/{empresa}
     * Exibe uma empresa específica.
     */
    public function show(Empresa $empresa)
    {
        // O "Route Model Binding" já encontrou a empresa pelo ID
        return $empresa;
    }

    /**
     * PUT /api/empresas/{empresa}
     * Atualiza uma empresa existente.
     */
    public function update(Request $request, Empresa $empresa)
    {
        $dadosValidados = $request->validate([
            'razaoSocial' => 'required|string|max:255',
            'cnpj' => 'required|string|max:20|unique:empresas,cnpj,' . $empresa->id, // Permite o CNPJ atual
            'setor' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:2',
        ]);

        // Atualiza os dados da empresa
        $empresa->update($dadosValidados);

        // Retorna a empresa atualizada
        return response()->json($empresa);
    }

    /**
     * DELETE /api/empresas/{empresa}
     * Exclui uma empresa.
     */
    public function destroy(Empresa $empresa)
    {
        // NOTA: Precisamos verificar se a empresa tem diagnósticos
        if ($empresa->diagnosticos()->count() > 0) {
            return response()->json([
                'message' => 'Não é possível excluir: Esta empresa já possui diagnósticos vinculados.'
            ], 409); // 409 Conflict
        }

        // Se não tiver, exclui a empresa
        $empresa->delete();

        // Retorna uma resposta vazia (No Content)
        return response()->noContent(); // 204
    }
}
