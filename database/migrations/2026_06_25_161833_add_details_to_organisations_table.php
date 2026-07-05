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
        Schema::table('organisations', function (Blueprint $table) {

            // Organisation Logo
            $table->string('logo')->nullable();

            // Short Description
            $table->text('description')->nullable();

            // Contact Information
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            // Registered Address
            $table->text('address')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organisations', function (Blueprint $table) {

            $table->dropColumn([
                'logo',
                'description',
                'email',
                'phone',
                'address'
            ]);

        });
    }
};