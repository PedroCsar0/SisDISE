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
        Schema::table('empresas', function (Blueprint $table) {
        // Adiciona a coluna 'user_id' que aponta para o criador
        $table->foreignId('user_id')
              ->nullable() // Permite nulo (se um Admin criar, etc.)
              ->after('id') // Coloca-a logo após o ID
              ->constrained('users') // Cria a chave estrangeira
              ->onDelete('set null'); // Se o usuário for excluído, mantém a empresa
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        $table->dropColumn('user_id');
        });
    }
};
