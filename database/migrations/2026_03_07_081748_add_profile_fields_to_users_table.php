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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['student', 'teacher'])->default('student')->after('name');
            $table->string('avatar')->nullable()->after('role');
            $table->string('class_name')->nullable()->after('avatar');
            $table->string('class_number')->nullable()->after('class_name');
            $table->string('subject')->nullable()->after('class_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'avatar', 'class_name', 'class_number', 'subject']);
        });
    }
};
