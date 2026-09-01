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
            if (empty($model->company_id) && auth()->check()) {
                $user = auth()->user();
                $tenantHeaderId = request()->header('X-Tenant-ID');
                if ($user->isSuperAdmin() && !empty($tenantHeaderId)) {
                    $model->company_id = (int) $tenantHeaderId;
                } elseif ($user->company_id) {
                    $model->company_id = $user->company_id;
                }
            }
        });

        // 2. Global Query Scope for tenant isolation
        static::addGlobalScope('tenant_isolation', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();

                // Check if Super Admin is operating inside a specific tenant via X-Tenant-ID header
                $tenantHeaderId = request()->header('X-Tenant-ID');
                if ($user->isSuperAdmin()) {
                    if (!empty($tenantHeaderId)) {
                        $builder->where($builder->getModel()->getTable() . '.company_id', (int) $tenantHeaderId);
                    }
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
