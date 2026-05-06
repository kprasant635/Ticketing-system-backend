<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('ups_user_uuid')->nullable()->unique()->after('id');
            $table->string('employee_code')->nullable()->after('ups_user_uuid');
            $table->string('role_name')->nullable()->after('employee_code');
            $table->string('phone')->nullable()->after('role_name');
            $table->string('designation')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ups_user_uuid', 'employee_code', 'role_name', 'phone', 'designation']);
        });
    }
};
