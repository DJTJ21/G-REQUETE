<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historique_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->nullable()->constrained('agents_scolarite')->nullOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('administrateurs')->nullOnDelete();
            $table->foreignId('requete_id')->nullable()->constrained('requetes')->nullOnDelete();
            $table->string('type_action', 80);
            $table->text('details')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->dateTime('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historique_actions');
    }
};
