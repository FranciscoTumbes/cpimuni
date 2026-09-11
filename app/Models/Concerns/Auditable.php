<?php

namespace App\Models\Concerns;

use App\Services\AuditService;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::registerModelEvent('created', function (Model $model): void {
            AuditService::record($model, 'CREAR', null, $model->getAttributes());
        });

        static::registerModelEvent('updated', function (Model $model): void {
            AuditService::record(
                $model,
                'ACTUALIZAR',
                $model->getOriginal(),
                $model->getAttributes()
            );
        });

        static::registerModelEvent('deleted', function (Model $model): void {
            AuditService::record($model, 'ELIMINAR', $model->getOriginal(), null);
        });
    }
}
