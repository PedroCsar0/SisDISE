<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_parametros', function (Blueprint $table) {
            $table->id();
            // Linka esta pergunta ao seu grupo (ex: Sdt1)
            $table->foreignId('grupo_parametro_id')->constrained('grupo_parametros');
            $table->string('codigo_item'); // Ex: "1.1.1", "2.3.4"
            $table->text('descricao');  // A pergunta em si
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_parametros');
    }
};