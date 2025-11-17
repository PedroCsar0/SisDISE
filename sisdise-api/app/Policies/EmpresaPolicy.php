<?php

namespace App\Policies;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmpresaPolicy
{
    use HandlesAuthorization;

    /**
     * Permite que Admins façam qualquer coisa (ignora outras regras)
     */
    public function before(User $user, $ability)
    {
        if ($user->tipo === 'Administrador') {
            return true;
        }
    }

    /**
     * Quem pode ver a lista de empresas?
     */
    public function viewAny(User $user)
    {
        return true; // Todos (logados) podem ver a lista
    }

    /**
     * Quem pode ver uma empresa específica?
     */
    public function view(User $user, Empresa $empresa)
    {
        return true;
    }

    /**
     * Quem pode criar empresas? (Apenas Admins e Avaliadores)
     */
    public function create(User $user)
    {
        return $user->tipo === 'Administrador' || $user->tipo === 'Avaliador';
    }

    /**
     * Quem pode atualizar uma empresa? (Apenas o criador)
     */
    public function update(User $user, Empresa $empresa)
    {
        // Permite se o ID do usuário logado for o mesmo do 'user_id' da empresa
        return $user->id === $empresa->user_id;
    }

    /**
     * Quem pode excluir uma empresa? (Apenas o criador)
     */
    public function delete(User $user, Empresa $empresa)
    {
        // Permite se o ID do usuário logado for o mesmo do 'user_id' da empresa
        return $user->id === $empresa->user_id;
    }
}
