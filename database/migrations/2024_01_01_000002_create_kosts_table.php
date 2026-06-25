<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kosts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('address');
            $table->string('city');
            $table->string('province');
            $table->string('postal_code', 10)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('type', ['Putra', 'Putri', 'Campur']);
            $table->decimal('price_monthly', 12, 2);
            $table->decimal('price_yearly', 12, 2)->nullable();
            $table->integer('total_rooms');
            $table->integer('available_rooms')->default(0);
            $table->json('facilities')->nullable()->comment('Array fasilitas kamar');
            $table->json('shared_facilities')->nullable()->comment('Fasilitas bersama');
            $table->string('owner_name');
            $table->string('owner_phone');
            $table->string('thumbnail')->nullable();
            $table->string('video_tour')->nullable();
            $table->enum('status', ['active', 'inactive', 'full'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->integer('min_stay')->default(1)->comment('Minimum stay in months');
            $table->json('rules')->nullable()->comment('Peraturan kost');
            $table->time('entry_time')->nullable()->comment('Jam masuk');
            $table->time('exit_time')->nullable()->comment('Jam keluar');
            $table->boolean('allow_cooking')->default(false);
            $table->boolean('allow_pets')->default(false);
            $table->boolean('allow_guest')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kosts');
    }
};
