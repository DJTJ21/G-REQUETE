<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requetes', function (Blueprint $table) {
            $table->id();
            $table->string('ref_requete', 20)->unique();
            $table->foreignId('etudiant_id')->constrained('etudiants');
            $table->foreignId('cours_id')->constrained('cours');
            $table->foreignId('session_id')->constrained('sessions_examens');
            $table->foreignId('agent_id')->nullable()->constrained('agents_scolarite')->nullOnDelete();
            $table->enum('type_anomalie', ['note_absente', 'note_erronee', 'incoherence_cc_sn', 'erreur_transcription']);
            $table->text('description')->nullable();
            $table->enum('statut', ['en_attente', 'en_cours_verification', 'traitee_fondee', 'traitee_non_fondee'])->default('en_attente');
            $table->text('motif_rejet')->nullable();
            $table->dateTime('date_soumission');
            $table->dateTime('date_prise_en_charge')->nullable();
            $table->dateTime('date_traitement')->nullable();
            $table->timestamps();

            $table->unique(['etudiant_id', 'cours_id', 'session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requetes');
    }
};
