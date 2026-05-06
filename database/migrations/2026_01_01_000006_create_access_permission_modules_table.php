<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $prefix = config('access.table_prefix', 'access_');

        Schema::create($prefix . 'permission_modules', function (Blueprint $table) use ($prefix) {
            $table->id();
            $table->uuid('permission_id');
            $table->uuid('module_id');
            $table->string('scope', 30)->default('all');
            $table->timestamps();

            $table->foreign('permission_id')
                  ->references('id')->on($prefix . 'permissions')
                  ->cascadeOnDelete();
            $table->foreign('module_id')
                  ->references('id')->on($prefix . 'modules')
                  ->cascadeOnDelete();
            $table->unique(['permission_id', 'module_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('access.table_prefix', 'access_') . 'permission_modules');
    }
};
