<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $prefix = config('access.table_prefix', 'access_');

        Schema::create($prefix . 'permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('group', 50);
            $table->string('action', 30);
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->jsonb('allowed_scopes')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestamps();

            $table->index('group');
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('access.table_prefix', 'access_') . 'permissions');
    }
};
