<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupo_parametros', function (Blueprint $table) {
            $table->id();
            $table->string('codigo'); // Ex: "Sdt1", "Sma2"
            $table->string('nome');   // Ex: "Proteção dos direitos humanos e trabalho"
            $table->double('peso');   // O peso "pi" da Tabela 4
            $table->integer('nota_maxima_grupo'); // O "Nmax" da Tabela 4 (70, 30, 20...)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupo_parametros');
    }
};