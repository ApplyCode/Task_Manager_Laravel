<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->uuid('squad_id')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('address');
            $table->boolean('gender');
            $table->boolean('public')->default(false);
            $table->string('status')->default('Not Started');
            $table->timestamp('birth_date')->nullable();
            $table->integer('working_days')->nullable();
            $table->json('allergies')->nullable();
            $table->json('surgeries')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')
              ->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('doctors');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
