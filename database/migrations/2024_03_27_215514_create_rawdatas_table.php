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
        Schema::create('rawdatas', function (Blueprint $table) {
            $table->id();
            $table->datetime('date');
            $table->string('crew_fullname',50)->nullable();
            $table->string('rev_col',50)->nullable();
            $table->string('dc',10)->nullable();
            $table->time('check_in_local',0)->nullable();
            $table->time('check_out_local',0)->nullable();
            $table->time('check_in_zulu',0)->nullable();
            $table->time('check_out_zulu',0)->nullable();
            $table->string('activity',20)->nullable();
            $table->string('remark',20)->nullable();

            $table->string('from_location',20)->nullable();
            $table->string('to_location',20)->nullable();

            $table->string("token",50)->unique();

            $table->time('departure_time_local',0)->nullable();
            $table->time('arrival_time_local',0)->nullable();

            $table->time('departure_time_zulu',0)->nullable();
            $table->time('arrival_time_zulu',0)->nullable();

            $table->string('ac_hotel',20)->nullable();
            $table->time('blh',0)->nullable();
            $table->time('flight_time',0)->nullable();
            $table->time('night_time',0)->nullable();
            $table->time('dur',0)->nullable();
            $table->string('ext',20)->nullable();
            $table->string('pax_booked',20)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rawdatas');
    }
};
