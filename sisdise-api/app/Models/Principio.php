<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importe o BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany;   // Importe o HasMany

class Principio extends Model
{
    use HasFactory;

    /**
     * Atributos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'diagnostico_id',
        'nomePrincipio',
        'pesoTotal',
        'escoreObtido',
    ];

    /**
     * Define o relacionamento: Um Princípio (deste diagnóstico) pertence a um Diagnóstico.
     */
    public function diagnostico(): BelongsTo
    {
        return $this->belongsTo(Diagnostico::class);
    }

    /**
     * Define o relacionamento: Um Princípio é composto por muitos Parâmetros de Avaliação.
     */
    public function parametroAvaliacaos(): HasMany // O Laravel entende o nome parametro_avaliacaos
    {
        return $this->hasMany(ParametroAvaliacao::class);
    }
}