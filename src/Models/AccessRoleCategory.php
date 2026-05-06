<?php

namespace VitalSaaS\VitalAccess\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AccessRoleCategory extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_system',
        'metadata',
        'is_active',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid()->toString();
            }
        });
    }

    public function getTable()
    {
        return config('access.table_prefix', 'access_') . 'role_categories';
    }

    /**
     * Get the roles that belong to this category.
     */
    public function roles(): HasMany
    {
        return $this->hasMany(AccessRole::class, 'category_id');
    }
}