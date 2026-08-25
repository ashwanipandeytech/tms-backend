<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenantTrait
{
    /**
     * Boot trait to automatically assign and filter queries by company_id.
     */
    protected static function bootBelongsToTenantTrait(): void
    {
        // 1. Auto-assign company_id on record creation
        static::creating(function ($model) {
            if (empty($model->company_id) && auth()->check() && auth()->user()->company_id) {
                $model->company_id = auth()->user()->company_id;
            }
        });

        // 2. Global Query Scope for tenant isolation
        static::addGlobalScope('tenant_isolation', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();

                // Super Admin bypasses global tenant scope to inspect all tenants
                if ($user->isSuperAdmin()) {
                    return;
                }

                if ($user->company_id) {
                    $builder->where($builder->getModel()->getTable() . '.company_id', $user->company_id);
                }
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
