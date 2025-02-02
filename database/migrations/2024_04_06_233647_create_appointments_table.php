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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->uuid('squad_id')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');//used
            $table->foreignId('patient_id')->nullable()->constrained()->onDelete('cascade');//used
            $table->timestamp('start_date')->nullable();//used
            $table->timestamp('due_date')->nullable();//used
            $table->timestamp('actual_start_date')->nullable();
            $table->integer('appointment_progress')->default(0);//used
            $table->boolean('completed')->default(false);//used
            $table->string('status')->default('Not Started');//used
            $table->string('category')->default('Uncategorized');
            $table->boolean('public')->default(false);//used
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('cascade');//used
            $table->json('team')->nullable();//used
            $table->json('labels')->nullable();//used
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('reminder_date')->nullable();
            $table->integer('cost')->nullable();
            $table->text('comments')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
