<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 253)->comment('Domain name, e.g. example.com');
            $table->unsignedSmallInteger('check_interval')->default(5)->comment('Check interval in minutes');
            $table->unsignedTinyInteger('timeout')->default(10)->comment('Request timeout in seconds');
            $table->enum('method', ['GET', 'HEAD'])->default('HEAD');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'name']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
