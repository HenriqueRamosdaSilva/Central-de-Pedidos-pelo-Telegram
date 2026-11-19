<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracoes', function (Blueprint $table) {
            $table->boolean('loja_pausada')->default(false);         
            $table->unsignedInteger('estoque_minimo')->default(5);    
            $table->string('chave_pix', 100)->nullable();
            $table->string('pix_nome', 25)->nullable();               
            $table->string('pix_cidade', 15)->nullable();             
            $table->string('codigo_vendedor', 20)->nullable();        
        });
    }

    public function down(): void
    {
        Schema::table('configuracoes', function (Blueprint $table) {
            $table->dropColumn(['loja_pausada', 'estoque_minimo', 'chave_pix', 'pix_nome', 'pix_cidade', 'codigo_vendedor']);
        });
    }
};
