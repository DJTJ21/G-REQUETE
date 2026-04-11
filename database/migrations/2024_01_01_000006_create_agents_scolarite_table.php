<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agents_scolarite', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('matricule_agent', 20)->nullable();
            $table->string('service', 100)->default('Scolarité');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agents_scolarite');
    }
};
