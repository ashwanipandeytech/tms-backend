<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait TenantScopedTrait
{
    /**
     * Boot the trait to add a global tenant scope when tenant/company context is active.
     */
    protected static function bootTenantScopedTrait(): void
    {
        static::creating(function (Model $model) {
            if (auth()->check()) {
                $user = auth()->user();
                $tenantHeaderId = request()->header('X-Tenant-ID');
                if ($user->isSuperAdmin() && !empty($tenantHeaderId)) {
                    if (empty($model->company_id) && \Schema::hasColumn($model->getTable(), 'company_id')) {
                        $model->company_id = (int) $tenantHeaderId;
                    }
                } elseif (isset($user->company_id) && empty($model->company_id) && \Schema::hasColumn($model->getTable(), 'company_id')) {
                    $model->company_id = $user->company_id;
                }
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();
                $tenantHeaderId = request()->header('X-Tenant-ID');

                if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
                    if (!empty($tenantHeaderId) && \Schema::hasColumn($builder->getModel()->getTable(), 'company_id')) {
                        $builder->where($builder->getModel()->getTable() . '.company_id', (int) $tenantHeaderId);
                    }
                    return;
                }

                if (isset($user->company_id) && $user->company_id !== null) {
                    if (\Schema::hasColumn($builder->getModel()->getTable(), 'company_id')) {
                        $builder->where($builder->getModel()->getTable() . '.company_id', $user->company_id);
                    }
                }
            }
        });
    }

    /**
     * Scope query without tenant restriction (for Super Admin or system tasks).
     */
    public function scopeWithoutTenant(Builder $query): Builder
    {
        return $query->withoutGlobalScope('tenant');
    }
}
