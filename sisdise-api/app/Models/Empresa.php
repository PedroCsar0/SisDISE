<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Empresa extends Model
{
    use HasFactory;

    /**
     * Atributos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'user_id', // O 'criador' da empresa
        'razaoSocial',
        'cnpj',
        'setor',
        'cidade',
        'estado',
    ];

    /**
     * Define o relacionamento: Uma Empresa pode ter muitos Diagnósticos.
     */
    public function diagnosticos(): HasMany
    {
        return $this->hasMany(Diagnostico::class);
    }

    /**
     * Define o relacionamento: Uma Empresa pode ter muitos Usuários (Gestores).
     */
    public function gestores(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Define o relacionamento: Uma Empresa pertence a um Usuário (Criador).
     */
    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
