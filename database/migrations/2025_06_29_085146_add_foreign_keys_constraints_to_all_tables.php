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
        // 1. إضافة قيود المفتاح الخارجي لجدول patients
        Schema::table('patients', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        // 2. إضافة قيود المفتاح الخارجي لجدول doctors
        Schema::table('doctors', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        // 3. إضافة قيود المفتاح الخارجي لجدول available_slots
        Schema::table('available_slots', function (Blueprint $table) {
            $table->foreign('doctor_id')
                ->references('id')
                ->on('doctors')
                ->onDelete('cascade');
        });

        // 4. إضافة قيود المفاتيح الخارجية لجدول appointments
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
                ->onDelete('cascade');

            $table->foreign('doctor_id')
                ->references('id')
                ->on('doctors')
                ->onDelete('cascade');
        });

        // 5. إضافة قيود المفاتيح الخارجية لجدول medical_records
        Schema::table('medical_records', function (Blueprint $table) {
            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
                ->onDelete('cascade');

            $table->foreign('doctor_id')
                ->references('id')
                ->on('doctors')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropForeign(['doctor_id']);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropForeign(['doctor_id']);
        });

        Schema::table('available_slots', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
