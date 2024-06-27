<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 10)->default('user')->after('name');
            $table->unsignedBigInteger('profile_id')->nullable()->after('role');
            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('set null');
        });
    }
    
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->dropForeign(['profile_id']);
            $table->dropColumn('profile_id');
        });
    }
};
