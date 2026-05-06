<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $prefix = config('access.table_prefix', 'access_');

        Schema::create($prefix . 'modules', function (Blueprint $table) use ($prefix) {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable();
            $table->string('name', 100);
            $table->string('slug', 80);
            $table->string('icon', 50)->nullable();
            $table->string('route', 200)->nullable();
            $table->string('type', 20)->default('menu');
            $table->integer('sort_order')->default(0);
            $table->integer('depth')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('plan_required', 30)->nullable();
            $table->boolean('is_visible')->default(true);
            $table->string('badge_type', 20)->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('parent_id');
            $table->index('slug');
            $table->index('type');
            $table->index('sort_order');
            $table->index('plan_required');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('access.table_prefix', 'access_') . 'modules');
    }
};
