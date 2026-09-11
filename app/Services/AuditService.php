<?php

namespace App\Services;

use App\Models\Auditoria;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    public static function record(
        Model $model,
        string $action,
        ?array $before = null,
        ?array $after = null,
        ?string $description = null,
        ?Request $request = null
    ): Auditoria {
        $attributes = self::sanitize($after ?? $before ?? $model->getAttributes());

        return self::event(
            $model->getTable(),
            $action,
            $model->getKey(),
            $model->getAttribute('municipalidad_id'),
            $before,
            $after ?? $attributes,
            $description ?? self::defaultDescription($action, $model),
            'EXITOSO',
            null,
            $request
        );
    }

    public static function event(
        string $table,
        string $action,
        ?int $recordId,
        ?int $municipalityId,
        ?array $before = null,
        ?array $after = null,
        ?string $description = null,
        string $result = 'EXITOSO',
        ?string $reason = null,
        ?Request $request = null,
        ?int $auditUserId = null
    ): Auditoria {
        $request ??= app()->bound('request') ? request() : null;
        $actor = Auth::user();

        return Auditoria::create([
            'municipalidad_id' => $municipalityId ?? $actor?->municipalidad_id,
            'usuario_id' => $auditUserId ?? $actor?->getAuthIdentifier(),
            'tabla' => $table,
            'registro_id' => $recordId,
            'accion' => $action,
            'descripcion' => $description,
            'datos_anteriores' => $before === null ? null : self::sanitize($before),
            'datos_nuevos' => $after === null ? null : self::sanitize($after),
            'resultado' => $result,
            'motivo' => $reason,
            'ip' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }

    private static function sanitize(array $attributes): array
    {
        foreach (['password', 'remember_token'] as $sensitive) {
            unset($attributes[$sensitive]);
        }

        return $attributes;
    }

    private static function defaultDescription(string $action, Model $model): string
    {
        return sprintf('%s sobre %s #%s.', $action, $model->getTable(), $model->getKey());
    }
}
