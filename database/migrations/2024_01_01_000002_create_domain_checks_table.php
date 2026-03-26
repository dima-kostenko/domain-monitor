<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domain_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['online', 'offline'])->index();
            $table->unsignedSmallInteger('response_code')->nullable()->comment('HTTP status code');
            $table->unsignedInteger('response_time')->nullable()->comment('Response time in milliseconds');
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['domain_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domain_checks');
    }
};
