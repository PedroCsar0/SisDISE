<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GrupoParametro;
use App\Models\ItemParametro;
use Illuminate\Http\Request;

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
}
