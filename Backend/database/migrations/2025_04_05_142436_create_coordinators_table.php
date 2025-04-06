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
        Schema::create('coordinators', function (Blueprint $table) {
            $table->id('Coor_ID');
            $table->string('Email');
            $table->string('Password');
            $table->string('FirstName');
            $table->string('LastName');
            $table->string('MiddleName')->nullable();
            $table->date('BirthDate');
            $table->enum('Sex', ['M', 'F']);
            $table->string('ContactNumber');
            $table->string('Address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coordinators');
    }
};
