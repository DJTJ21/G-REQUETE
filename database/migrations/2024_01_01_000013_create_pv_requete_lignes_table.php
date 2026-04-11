<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pv_requete_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pv_id')->constrained('pv_requetes')->cascadeOnDelete();
            $table->foreignId('requete_id')->constrained('requetes');
            $table->smallInteger('ordre_affichage')->default(0);
            $table->timestamps();

            $table->unique(['pv_id', 'requete_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pv_requete_lignes');
    }
};
