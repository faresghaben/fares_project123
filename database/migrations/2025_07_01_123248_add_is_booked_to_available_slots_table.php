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
        Schema::table('available_slots', function (Blueprint $table) {
            // إضافة عمود is_booked كقيمة منطقية (boolean) بقيمة افتراضية false
            // after('end_time') لوضع العمود بعد عمود end_time (اختياري، يمكنك حذفها)
            $table->boolean('is_booked')->default(false)->after('end_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('available_slots', function (Blueprint $table) {
            // حذف عمود is_booked إذا تم التراجع عن الهجرة
            $table->dropColumn('is_booked');
        });
    }
};