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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id'); // سيتم تحويله لعلاقة لاحقاً
            $table->unsignedBigInteger('doctor_id'); // سيتم تحويله لعلاقة لاحقاً
            $table->text('diagnosis');//التشخيص
            $table->text('treatment')->nullable();//العلاج
            $table->date ('record_date');//تاريخ التشخيص
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
        Schema::dropIfExists('medical_records');
    }
};
