
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip')->unique()->after('email');
            $table->string('phone')->nullable()->after('nip');
            $table->enum('role', ['admin', 'kepala_sekolah', 'guru', 'staff'])->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nip', 'phone', 'role']);
        });
    }
};
