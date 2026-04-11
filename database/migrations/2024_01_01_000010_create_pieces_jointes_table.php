<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pieces_jointes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requete_id')->constrained('requetes')->cascadeOnDelete();
            $table->string('nom_fichier', 255);
            $table->string('chemin_fichier', 500);
            $table->string('type_mime', 50);
            $table->bigInteger('taille');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pieces_jointes');
    }
};
