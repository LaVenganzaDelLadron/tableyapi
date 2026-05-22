<?php

namespace App\Http\Requests\Concerns;

trait ResolvesRouteIds
{
    protected function routeId(string $key): mixed
    {
        $value = $this->route($key);

        return is_object($value) && isset($value->id)
            ? $value->id
            : ($value ?? $this->route('id'));
    }
}
