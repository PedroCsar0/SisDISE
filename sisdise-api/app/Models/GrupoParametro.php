<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GrupoParametro extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nome',
        'peso',
        'nota_maxima_grupo',
    ];

    // Um Grupo (Sdt1) tem muitos Itens (perguntas)
    public function itemParametros(): HasMany
    {
        return $this->hasMany(ItemParametro::class);
    }
}