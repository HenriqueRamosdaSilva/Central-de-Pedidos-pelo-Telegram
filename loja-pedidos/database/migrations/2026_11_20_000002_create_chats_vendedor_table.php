<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chats_vendedor', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chat_id')->unique();   
            $table->string('nome', 150);
            $table->string('tipo', 20)->default('private');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chats_vendedor');
    }
};
