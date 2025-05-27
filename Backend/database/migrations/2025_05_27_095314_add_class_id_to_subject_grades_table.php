<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClassIdToSubjectGradesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subject_grades', function (Blueprint $table) {
            $table->unsignedBigInteger('Class_ID')->nullable()->after('Subject_ID');
            $table->foreign('Class_ID')->references('Class_ID')->on('classes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subject_grades', function (Blueprint $table) {
            $table->dropForeign(['Class_ID']);
            $table->dropColumn('Class_ID');
        });
    }
}
