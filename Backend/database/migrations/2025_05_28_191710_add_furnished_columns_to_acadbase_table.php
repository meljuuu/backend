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
        Schema::table('acadbase', function (Blueprint $table) {
            $table->timestamp('furnished_date')->nullable();
            $table->string('furnished_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acadbase', function (Blueprint $table) {
            $table->dropColumn(['furnished_date', 'furnished_by']);
        });
    }
};
