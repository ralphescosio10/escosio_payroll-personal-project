<?php

namespace App;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditLogger
{
    public function record(Request $request, Model $model, string $action, array $changes): void
    {
        AuditLog::query()->create([
            'user_id' => $request->user()?->getAuthIdentifier(),
            'model_type' => $model::class,
            'model_id' => $model->getKey(),
            'action' => $action,
            'changes' => $changes,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    public function snapshot(Model $model): array
    {
        return array_diff_key($model->getAttributes(), [
            'created_at' => true,
            'updated_at' => true,
        ]);
    }

    public function changes(array $before, Model $model): array
    {
        $after = $this->snapshot($model);
        $changedKeys = array_keys(array_filter(
            $after,
            fn (mixed $value, string $key): bool => ! array_key_exists($key, $before) || $before[$key] !== $value,
            ARRAY_FILTER_USE_BOTH
        ));

        return [
            'before' => array_intersect_key($before, array_flip($changedKeys)),
            'after' => array_intersect_key($after, array_flip($changedKeys)),
        ];
    }
}
