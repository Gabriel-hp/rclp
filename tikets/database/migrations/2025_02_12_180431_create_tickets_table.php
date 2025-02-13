<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('protocolo')->unique();
            $table->string('assunto');
            $table->timestamp('abertoEm');
            $table->string('criadoPor');
            $table->timestamp('resolvidoEm')->nullable();
            $table->timestamp('fechadoEm')->nullable();
            $table->enum('status', ['Em Aberto', 'Aguardando', 'Fechado']);
            $table->string('origem');
            $table->enum('nivel', ['Junior', 'Pleno', 'Senior'])->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
