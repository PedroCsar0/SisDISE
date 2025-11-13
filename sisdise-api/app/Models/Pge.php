<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importe o BelongsTo

class Pge extends Model
{
    use HasFactory;

    /**
     * Atributos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'diagnostico_id',
        'dataCriacao',
        'status',
    ];

    /**
     * Define o relacionamento: Um PGES pertence a um Diagnóstico.
     */
    public function diagnostico(): BelongsTo
    {
        return $this->belongsTo(Diagnostico::class);
    }
}