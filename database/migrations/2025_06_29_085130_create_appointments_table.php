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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id'); // سيتم تحويله لعلاقة لاحقاً
            $table->unsignedBigInteger('doctor_id'); // سيتم تحويله لعلاقة لاحقاً
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->enum('status', ['scheduled', 'completed', 'canceled'])->default('scheduled');
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
};
