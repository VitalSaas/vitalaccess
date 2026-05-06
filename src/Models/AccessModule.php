<?php

namespace VitalSaaS\VitalAccess\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AccessModule extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'access_modules';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'icon',
        'route',
        'type',
        'sort_order',
        'depth',
        'is_active',
        'plan_required',
        'is_visible',
        'badge_type',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'is_visible' => 'boolean',
        'sort_order' => 'integer',
        'depth' => 'integer'
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

    public function parent()
    {
        return $this->belongsTo(AccessModule::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(AccessModule::class, 'parent_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(AccessPermission::class, 'access_permission_modules', 'module_id', 'permission_id');
    }
}