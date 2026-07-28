<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function getConnection(): ?string
    {
        return config('change-requests.connection');
    }

    public function up(): void
    {
        if (Schema::connection($this->getConnection())->hasColumn('change_requests', 'source_app')) {
            return;
        }

        Schema::connection($this->getConnection())->table('change_requests', function (Blueprint $table) {
            // Default 'schoolaid': todos los tickets existentes son suyos y su
            // código no declara la columna, así que no se entera del cambio.
            $table->string('source_app', 50)->default('schoolaid')->index()->after('school_name');
        });
    }

    public function down(): void
    {
        if (! Schema::connection($this->getConnection())->hasColumn('change_requests', 'source_app')) {
            return;
        }

        Schema::connection($this->getConnection())->table('change_requests', function (Blueprint $table) {
            $table->dropColumn('source_app');
        });
    }
};
