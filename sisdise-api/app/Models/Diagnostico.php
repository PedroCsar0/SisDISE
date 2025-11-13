<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importe o BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany;   // Importe o HasMany
use Illuminate\Database\Eloquent\Relations\HasOne;     // Importe o HasOne

class Diagnostico extends Model
{
    use HasFactory;

    /**
     * Atributos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'user_id',
        'empresa_id',
        'dataAnalise',
        'escoreFinal',
        'classificacao',
    ];

    /**
     * Define o relacionamento: Um Diagnóstico pertence a um Usuário.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define o relacionamento: Um Diagnóstico pertence a uma Empresa.
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Define o relacionamento: Um Diagnóstico contém muitos Princípios.
     */
    public function principios(): HasMany
    {
        return $this->hasMany(Principio::class);
    }

    /**
     * Define o relacionamento: Um Diagnóstico gera um Plano de Gestão (PGES).
     */
    public function pge(): HasOne
    {
        return $this->hasOne(Pge::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(DiagnosticoItem::class);
    }
}
