<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('ciclo'); // 1–10
            $table->string('nombre');
            $table->unsignedTinyInteger('creditos')->nullable();
            $table->string('tipo')->nullable(); // General | Específico | Especialidad | Electivo
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cursos');
    }
};
