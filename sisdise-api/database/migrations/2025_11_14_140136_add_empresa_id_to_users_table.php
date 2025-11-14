<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Adiciona a coluna 'empresa_id' após a coluna 'tipo'
            $table->foreignId('empresa_id')
                  ->nullable() // Permite que seja nulo (para Admins/Avaliadores)
                  ->after('tipo')
                  ->constrained('empresas') // Cria a chave estrangeira
                  ->nullOnDelete(); // Se a empresa for deletada, define o ID do usuário como nulo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
            $table->dropColumn('empresa_id');
        });
    }
};
