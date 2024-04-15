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
    Schema::create('users', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('email')->unique();
      $table->timestamp('email_verified_at')->nullable();
      $table->string('password');
      $table->string('license_number')->nullable();
      $table->string('contact')->nullable();
      $table->string('department')->nullable();
      $table->date('date_of_birth')->nullable();
      $table->string('gender')->nullable();
      $table->text('address')->nullable();
      $table->double('latitude', 10, 8)->nullable();
      $table->double('longitude', 10, 8)->nullable();
      $table->rememberToken();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('users');
  }
};
