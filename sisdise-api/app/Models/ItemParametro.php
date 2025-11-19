<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemParametro extends Model
{
    use HasFactory;

    protected $fillable = [
        'grupo_parametro_id',
        'codigo_item',
        'descricao',
        'recomendacao',
    ];

    // Uma Pergunta (Item) pertence a um Grupo
    public function grupoParametro(): BelongsTo
    {
        return $this->belongsTo(GrupoParametro::class);
    }
}
