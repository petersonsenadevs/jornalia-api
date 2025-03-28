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
        Schema::table('hours_worked', function (Blueprint $table) {
            $table->decimal('night_hours', 8, 2)->default(0)->after('overtime_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hour_workeds', function (Blueprint $table) {
            $table->dropColumn('night_hours');
        });
    }
};
