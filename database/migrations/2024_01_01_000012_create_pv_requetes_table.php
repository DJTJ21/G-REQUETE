<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pv_requetes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('agents_scolarite');
            $table->foreignId('session_id')->constrained('sessions_examens');
            $table->string('ref_pv', 30)->unique();
            $table->dateTime('date_generation');
            $table->string('chemin_pdf', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pv_requetes');
    }
};
