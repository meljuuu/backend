<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('research', function (Blueprint $table) {
            $table->bigIncrements('Research_ID')->unsigned();
            $table->bigInteger('Teacher_ID')->unsigned();
            $table->string('Title', 255);
            $table->text('Abstract');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('Teacher_ID')
                  ->references('Teacher_ID')
                  ->on('teachers')
                  ->onDelete('cascade');

            // Indexes
            $table->index('Teacher_ID');
            $table->index('Research_ID');
        });
    }

    public function down()
    {
        Schema::dropIfExists('research');
    }
};