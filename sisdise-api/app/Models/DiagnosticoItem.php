<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- ADICIONE O IMPORT

class DiagnosticoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'diagnostico_id',
        'item_parametro_id',
        'nota',
    ];

    /**
     * Define o relacionamento: Esta Resposta (Item) pertence a um Template (Pergunta).
     */
    public function itemParametro(): BelongsTo
    {
        return $this->belongsTo(ItemParametro::class);
    }
}
