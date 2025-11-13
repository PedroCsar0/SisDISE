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
        Schema::create('parametro_avaliacaos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('principio_id')->constrained('principios'); // Relacionamento com Principio
            $table->string('descricao');
            $table->integer('nota'); // A nota de 0 a 5
            $table->double('peso');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametro_avaliacaos');
    }
};
