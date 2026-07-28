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
        // La BD es compartida: en los entornos donde schoolaid-admin ya creó la
        // tabla, esta migración es un no-op deliberado.
        if (Schema::connection($this->getConnection())->hasTable('change_requests')) {
            return;
        }

        Schema::connection($this->getConnection())->create('change_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->text('description');
            $table->string('category')->nullable();
            $table->string('priority')->default('medium');
            $table->string('status')->default('open')->index();
            $table->string('created_by_id')->index();
            $table->string('created_by_name');
            $table->string('created_by_email');
            $table->string('school_id')->nullable()->index();
            $table->string('school_name')->nullable();
            $table->string('last_actor')->default('admin');
            $table->json('messages');
            $table->json('metadata')->nullable();
            $table->json('images')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * No-op a propósito: un rollback dropearía la tabla compartida con
     * schoolaid-admin y la app-agente. Para tirarla, hacerlo a mano.
     */
    public function down(): void
    {
    }
};
