<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'organisateur', 'participant'])->default('participant')->after('email');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('role');
            $table->string('avatar')->nullable()->after('status');
            $table->string('phone')->nullable()->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'avatar', 'phone']);
        });
    }
};
