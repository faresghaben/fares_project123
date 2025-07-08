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
        Schema::table('patients', function (Blueprint $table) {
            // إضافة عمود chosen_doctor_id كـ foreign key يشير إلى جدول doctors
            $table->foreignId('chosen_doctor_id')
                  ->nullable() // يمكن أن يكون المريض ليس لديه طبيب مختار بعد
                  ->constrained('doctors') // يشير إلى جدول doctors
                  ->onDelete('set null'); // إذا تم حذف الطبيب، يصبح هذا العمود null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // حذف الـ foreign key أولاً
            $table->dropConstrainedForeignId('chosen_doctor_id');
            // ثم حذف العمود
            $table->dropColumn('chosen_doctor_id');
        });
    }
};