<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStampedPdfStorageToAcadbaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acadbase', function (Blueprint $table) {
            $table->string('stamped_pdf_storage')->nullable()->after('pdf_storage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acadbase', function (Blueprint $table) {
            $table->dropColumn('stamped_pdf_storage');
        });
    }
}
