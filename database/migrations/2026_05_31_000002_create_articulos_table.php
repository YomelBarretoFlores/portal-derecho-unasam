<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articulos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->json('autores')->nullable();
            $table->string('paginas')->nullable();
            $table->string('categoria')->nullable();
            $table->text('resumen')->nullable();
            $table->date('fecha')->nullable();
            $table->string('doi')->nullable();
            $table->unsignedInteger('descargas')->default(0);
            $table->boolean('publicado')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articulos');
    }
};
