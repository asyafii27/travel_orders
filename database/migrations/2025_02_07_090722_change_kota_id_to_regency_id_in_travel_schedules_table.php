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
        Schema::table('travel_schedules', function (Blueprint $table) {
            $table->renameColumn('kota_from_id', 'regency_from_id');
            $table->renameColumn('kota_to_id', 'regency_to_id');
            $table->integer('regency_from_id')->index()->constrained('regencies')->onDelete('cascade')->change();
            $table->integer('regency_to_id')->index()->constrainded('regencies')->onDelete('cascade')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       //
    }
};
