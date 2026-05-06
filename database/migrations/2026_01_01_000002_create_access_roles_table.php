<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $prefix = config('access.table_prefix', 'access_');

        Schema::create($prefix . 'roles', function (Blueprint $table) use ($prefix) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->string('slug', 80);
            $table->text('description')->nullable();
            $table->uuid('category_id')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('level')->default(0);
            $table->jsonb('metadata')->nullable();
            $table->string('scope_type')->nullable();
            $table->uuid('scope_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')
                  ->references('id')->on($prefix . 'role_categories')
                  ->nullOnDelete();
            $table->index(['scope_type', 'scope_id']);
            $table->index('level');
            $table->unique(['slug', 'scope_type', 'scope_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('access.table_prefix', 'access_') . 'roles');
    }
};
