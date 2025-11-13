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
        Schema::table('parametro_avaliacaos', function (Blueprint $table) {
            $table->double('escore_obtido')->default(0)->after('peso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parametro_avaliacaos', function (Blueprint $table) {
            //
        });
    }
};
