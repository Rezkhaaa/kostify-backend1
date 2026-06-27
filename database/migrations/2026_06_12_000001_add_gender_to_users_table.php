<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'gender')) {
            Schema::table('users', function (Blueprint $table) {
                // # Kategori penghuni dipakai untuk mencocokkan Kos Putra/Putri/Campuran.
                // # Nilai sengaja sama dengan properties.gender_type agar validasinya mudah dipahami.
                $table->enum('gender', ['putra', 'putri'])->nullable()->after('role');
            });
        }

        // # Seeder lama mungkin belum mengisi gender. Default ini aman untuk data demo.
        DB::table('users')->where('role', 'tenant')->whereNull('gender')->update(['gender' => 'putri']);
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'gender')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('gender');
            });
        }
    }
};
