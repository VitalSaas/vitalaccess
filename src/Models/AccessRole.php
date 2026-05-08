<?php

namespace VitalSaaS\VitalAccess\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AccessRole extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'access_roles';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category_id',
        'is_system',
        'is_active',
        'level',
        'metadata',
        'scope_type',
        'scope_id'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'level' => 'integer'
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

    public function permissions()
    {
        return $this->belongsToMany(AccessPermission::class, 'access_role_permissions', 'role_id', 'permission_id');
    }

    public function users()
    {
        return $this->belongsToMany(config('auth.providers.users.model'), 'access_user_roles', 'role_id', 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(AccessRoleCategory::class, 'category_id');
    }
}