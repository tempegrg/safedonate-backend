<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organisation_applications', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('cascade');

            $table->string('organisation_name');

            $table->string('organisation_type');

            $table->string('registration_number');

            $table->text('description');

            $table->string('email');

            $table->string('phone');

            $table->text('address');

            $table->string('website');

            $table->string('certificate_path')
                  ->nullable();

            $table->string('supporting_document_path')
                  ->nullable();
                  
            $table->string('logo_path')
                  ->nullable();

            $table->enum(
                'status',
                [
                    'pending',
                    'verified',
                    'rejected'
                ]
            )->default('pending');

            $table->text('admin_remark')
                  ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'organisation_applications'
        );
    }
};