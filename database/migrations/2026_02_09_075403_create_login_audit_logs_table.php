<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('login_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('status'); // 'success', 'failed', 'locked'
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('failure_reason')->nullable(); // 'invalid_credentials', 'account_locked', etc.
            $table->integer('attempts_count')->default(1);
            $table->timestamp('locked_until')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('login_audit_logs');
    }
};