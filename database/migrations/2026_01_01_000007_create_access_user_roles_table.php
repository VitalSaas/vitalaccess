<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $prefix = config('access.table_prefix', 'access_');
        $usersTable = config('access.users_table', 'users');

        Schema::create($prefix . 'user_roles', function (Blueprint $table) use ($prefix) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('user_id');
            $table->uuid('role_id');
            $table->string('scope_type')->nullable();
            $table->uuid('scope_id')->nullable();
            $table->timestamp('granted_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->uuid('granted_by')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('role_id')
                  ->references('id')->on($prefix . 'roles')
                  ->cascadeOnDelete();
            $table->unique(['user_id', 'role_id', 'scope_type', 'scope_id']);
            $table->index(['scope_type', 'scope_id']);
            $table->index('expires_at');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('access.table_prefix', 'access_') . 'user_roles');
    }
};
