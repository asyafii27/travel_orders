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
        Schema::create('travel_schedules', function (Blueprint $table) {
            $table->id();
            $table->integer('kota_from_id')->index()->constrained('kota')->onDelete('cascade');
            $table->integer('kota_to_id')->index()->constrainded('kota')->onDelete('cascade');
            $table->dateTime('departure_start')->index();
            $table->dateTime('departure_finish')->index();
            $table->integer('quota');
            $table->decimal('ticket_price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_schedules');
    }
};
