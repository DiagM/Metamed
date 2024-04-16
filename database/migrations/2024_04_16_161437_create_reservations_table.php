<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up()
  {
    Schema::create('reservations', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->unsignedBigInteger('doctor_id');
      $table->unsignedBigInteger('patient_id');
      $table->dateTime('start_datetime');
      $table->dateTime('end_datetime');
      $table->text('description')->nullable();
      $table->timestamps();

      // Define foreign key constraints
      $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
      $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('reservations');
  }
};
