<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requete_id')->unique()->constrained('requetes')->cascadeOnDelete();
            $table->foreignId('etudiant_id')->constrained('etudiants');
            $table->foreignId('cours_id')->constrained('cours');
            $table->decimal('note_avant', 5, 2)->nullable();
            $table->decimal('note_apres', 5, 2)->nullable();
            $table->foreignId('modifie_par')->constrained('agents_scolarite');
            $table->dateTime('date_modification');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
