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
            if (auth()->check() && isset(auth()->user()->company_id)) {
                if (empty($model->company_id) && \Schema::hasColumn($model->getTable(), 'company_id')) {
                    $model->company_id = auth()->user()->company_id;
                }
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check() && isset(auth()->user()->company_id) && auth()->user()->company_id !== null) {
                // If not Super Admin (who bypasses tenant filter)
                if (!method_exists(auth()->user(), 'isSuperAdmin') || !auth()->user()->isSuperAdmin()) {
                    if (\Schema::hasColumn($builder->getModel()->getTable(), 'company_id')) {
                        $builder->where($builder->getModel()->getTable() . '.company_id', auth()->user()->company_id);
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
