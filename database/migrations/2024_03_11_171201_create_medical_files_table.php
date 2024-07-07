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
    Schema::create('medical_files', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('patient_id');
      $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
      $table->unsignedBigInteger('doctor_id');
      $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
      $table->string('file_name'); // Name of the medical file
      $table->text('description')->nullable(); // Description of the medical file
      $table->string('file_path')->nullable(); // Path to the medical file
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('medical_files');
  }
};
