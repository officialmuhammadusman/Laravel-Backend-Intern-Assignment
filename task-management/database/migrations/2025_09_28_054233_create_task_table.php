<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) { 
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->datetime('due_date')->nullable();
            $table->foreignId('project_id')->constrained('projects'); 
            $table->foreignId('assigned_to')->constrained('users'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks'); 
    }
};