<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications_app', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('requete_id')->nullable()->constrained('requetes')->cascadeOnDelete();
            $table->text('message');
            $table->enum('canal', ['app', 'email'])->default('app');
            $table->boolean('est_lue')->default(false);
            $table->dateTime('date_envoi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications_app');
    }
};
