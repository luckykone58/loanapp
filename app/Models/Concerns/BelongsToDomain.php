<?php

namespace App\Models\Concerns;

use App\Scopes\DomainScope;

trait BelongsToDomain
{
    public static function bootBelongsToDomain(): void
    {
        static::addGlobalScope(new DomainScope());

        static::creating(function ($model): void {
            if (app()->bound('currentDomain') && empty($model->domain_id)) {
                $model->domain_id = app('currentDomain')->id;
            }
        });
    }
}




