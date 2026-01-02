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
        Schema::create('identity_documents', function (Blueprint $table) {
            $table->id('id')->primary();
            $table->unsignedBigInteger('user_id');

            $table->enum('type', ['NRC', 'Passport', 'Driver License']);
            $table->string('document_number')->unique();
            $table->string('issued_country');
            $table->date('issued_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('document_file')->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('identity_documents');
    }
};
