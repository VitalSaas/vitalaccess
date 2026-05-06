<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $prefix = config('access.table_prefix', 'access_');

        Schema::create($prefix . 'role_permissions', function (Blueprint $table) use ($prefix) {
            $table->id();
            $table->uuid('role_id');
            $table->uuid('permission_id');
            $table->string('scope', 30)->default('all');
            $table->boolean('is_denied')->default(false);
            $table->timestamps();

            $table->foreign('role_id')
                  ->references('id')->on($prefix . 'roles')
                  ->cascadeOnDelete();
            $table->foreign('permission_id')
                  ->references('id')->on($prefix . 'permissions')
                  ->cascadeOnDelete();
            $table->unique(['role_id', 'permission_id']);
            $table->index('scope');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('access.table_prefix', 'access_') . 'role_permissions');
    }
};
