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
        Schema::table('users', function (Blueprint $table) {
            // إضافة عمود 'role' كنوع ENUM مع القيم الافتراضية
            $table->enum('role', ['patient', 'doctor', 'admin'])->default('patient')->after('password');
        });
    }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('users', function (Blueprint $table) {
                // حذف عمود 'role' عند التراجع عن Migration
                $table->dropColumn('role');
            });
        }
    };  
