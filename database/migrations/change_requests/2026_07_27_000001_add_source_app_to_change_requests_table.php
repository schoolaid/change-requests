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

    /**
     * No-op a propósito: `source_app` es el discriminador de app para las tres
     * consumidoras de la tabla compartida (schoolaid-admin, la app-agente y
     * marketaid). Dropearla borraría, para todas las filas de las tres apps,
     * el único dato que dice quién es dueño de cada ticket — y no es
     * recuperable desde ninguna otra columna. Si alguna vez hace falta
     * quitarla, es un paso de runbook manual coordinado con los otros
     * equipos, no un rollback de artisan.
     */
    public function down(): void
    {
    }
};
