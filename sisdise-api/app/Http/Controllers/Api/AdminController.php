<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GrupoParametro;
use App\Models\ItemParametro;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * GET /api/admin/parametros
     */
    public function getParametros()
    {
        return GrupoParametro::with('itemParametros')->get();
    }

    /**
     * PUT /api/admin/parametros/{itemParametro}
     */
    public function updateParametro(Request $request, ItemParametro $itemParametro)
    {
        $dadosValidados = $request->validate([
            'descricao' => 'required|string',
            'codigo_item' => 'required|string',
        ]);
        $itemParametro->update($dadosValidados);
        return response()->json($itemParametro);
    }

    /**
     * PUT /api/admin/grupos/{grupoParametro}
     */
    public function updateGrupo(Request $request, GrupoParametro $grupoParametro)
    {
        $dadosValidados = $request->validate([
            'nome' => 'required|string',
            'peso' => 'required|numeric|min:0',
            'nota_maxima_grupo' => 'required|integer|min:0',
        ]);
        $grupoParametro->update($dadosValidados);
        return response()->json($grupoParametro);
    }

    public function getUsers()
    {
        return User::orderBy('created_at', 'desc')->get();
    }

    /**
     * GET /api/admin/users/{user}
     * Mostra detalhes do usuário com seus relacionamentos.
     */
    public function getUserDetails(User $user)
    {
        // Carrega relacionamentos baseados no tipo
        if ($user->tipo === 'Avaliador') {
            // Para Avaliador: Carrega os diagnósticos que ele fez e as empresas desses diagnósticos
            $user->load(['diagnosticos.empresa']);
        } elseif ($user->tipo === 'Gestor Empresarial') {
            // Para Gestor: Carrega a empresa à qual ele pertence
            $user->load(['empresa']);
        }

        return $user;
    }

    public function updateUser(Request $request, User $user)
    {
        $dadosValidados = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id), // Ignora o email atual do próprio usuário
            ],
            'tipo' => 'required|string|in:Avaliador,Gestor Empresarial,Administrador',
            'empresa_id' => 'nullable|integer|exists:empresas,id',

            // Validação de Senha:
            // 1. nullable: Só valida se não for nulo (vazio).
            // 2. confirmed: Exige um campo 'password_confirmation' no frontend.
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Atualiza os dados principais
        $user->name = $dadosValidados['name'];
        $user->email = $dadosValidados['email'];
        $user->tipo = $dadosValidados['tipo'];

        // Se o tipo for Gestor, vincula a empresa. Se não, desvincula (null).
        $user->empresa_id = ($dadosValidados['tipo'] === 'Gestor Empresarial') ? $dadosValidados['empresa_id'] : null;

        // Atualiza a senha APENAS se uma nova senha foi enviada
        if ($request->filled('password')) {
            $user->password = Hash::make($dadosValidados['password']);
        }

        $user->save(); // Salva as alterações

        return response()->json($user);
    }

    // --- MÉTODO NOVO: EXCLUIR USUÁRIO ---
    /**
     * DELETE /api/admin/users/{user}
     * Exclui um usuário.
     */
    public function deleteUser(Request $request, User $user)
    {
        // Medida de segurança: Admin não pode excluir a si mesmo
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Você não pode excluir sua própria conta.'], 403);
        }

        // NOTA: Aqui você pode adicionar lógica para reatribuir diagnósticos,
        // mas por enquanto, vamos apenas excluir.

        $user->delete();

        return response()->noContent(); // Retorna 204 (Sucesso, sem conteúdo)
    }
}
