<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importe o BelongsTo

class ParametroAvaliacao extends Model
{
    use HasFactory;

    /**
     * Atributos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'principio_id',
        'descricao',
        'nota',
        'peso',
    ];

    /**
     * Define o relacionamento: Um Parâmetro (desta avaliação) pertence a um Princípio.
     */
    public function principio(): BelongsTo
    {
        return $this->belongsTo(Principio::class);
    }
}