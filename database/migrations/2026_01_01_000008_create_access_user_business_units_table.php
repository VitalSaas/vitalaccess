<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $prefix = config('access.table_prefix', 'access_');

        Schema::create($prefix . 'user_business_units', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('business_unit_type', 100);
            $table->uuid('business_unit_id');
            $table->boolean('is_default')->default(false);
            $table->jsonb('metadata')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['business_unit_type', 'business_unit_id'], 'access_ubu_type_id_idx');
            $table->index('user_id', 'access_ubu_user_id_idx');
            $table->unique(['user_id', 'business_unit_type', 'business_unit_id'], 'access_ubu_unique_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('access.table_prefix', 'access_') . 'user_business_units');
    }
};
