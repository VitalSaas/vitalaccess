<?php

namespace VitalSaaS\VitalAccess\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AccessPermission extends Model
{
    use HasFactory;

    protected $table = 'access_permissions';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'slug',
        'group',
        'action',
        'description',
        'is_system',
        'allowed_scopes',
        'metadata'
    ];

    protected $casts = [
        'allowed_scopes' => 'array',
        'metadata' => 'array',
        'is_system' => 'boolean'
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

    public function roles()
    {
        return $this->belongsToMany(AccessRole::class, 'access_role_permissions', 'permission_id', 'role_id');
    }

    public function modules()
    {
        return $this->belongsToMany(AccessModule::class, 'access_permission_modules', 'permission_id', 'module_id');
    }
}