<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->decimal('taxa_entrega', 10, 2)->default(0);
            $table->string('forma_pagamento', 20)->nullable();   
            $table->decimal('troco_para', 10, 2)->nullable();   
            $table->text('observacao')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['taxa_entrega', 'forma_pagamento', 'troco_para', 'observacao']);
        });
    }
};
