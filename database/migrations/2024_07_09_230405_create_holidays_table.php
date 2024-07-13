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
    Schema::create('holidays', function (Blueprint $table) {
      $table->id();
      $table->string('reason'); // Reason for the holiday
      $table->date('date_start'); // Start date of the holiday
      $table->date('date_end'); // End date of the holiday
      $table->string('status'); // Status of the holiday (requested, approved, etc.)
      $table->unsignedBigInteger('department_id');
      $table->foreign('department_id')->references('id')->on('users')->onDelete('cascade');
      $table->unsignedBigInteger('doctor_id');
      $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
      $table->timestamps();;
    });
  }


  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('holidays');
  }
};
