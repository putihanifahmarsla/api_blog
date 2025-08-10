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
        Schema::create('log_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action', 30); // create, update, delete
            $table->string('entity');
            $table->unsignedBigInteger('entity_id');
            $table->json('details')->nullable();
            $table->enum('status', ['success', 'failure'])->default('success');
            $table->text('error_message')->nullable();
            $table->string('ip_address', 32)->nullable();
            $table->string('user_agent', 100)->nullable();
            $table->string('module')->nullable();
            $table->string('request_method', 30)->nullable(); // GET, POST, PUT, DELETE
            $table->text('url_accessed')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_activities');
    }
};
