<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Importe o HasMany

class Empresa extends Model
{
    use HasFactory;

    /**
     * Atributos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'razaoSocial',
        'cnpj',
        'setor',
        'cidade',
        'estado',
    ];

    /**
     * Define o relacionamento: Uma Empresa pode ter muitos Diagnósticos (ao longo do tempo).
     */
    public function diagnosticos(): HasMany
    {
        return $this->hasMany(Diagnostico::class);
    }

    public function gestores(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
