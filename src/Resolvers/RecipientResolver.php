<?php

namespace NotificationSystem\Resolvers;

use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use NotificationSystem\Contracts\RecipientResolverInterface;
use NotificationSystem\DTOs\RecipientData;

/**
 * Default implementation of {@see RecipientResolverInterface}.
 *
 * Resolves a wide range of recipient target types into a unified
 * Collection of {@see RecipientData} DTOs:
 *
 * - Single Eloquent Model
 * - Eloquent Collection
 * - LazyCollection
 * - Eloquent QueryBuilder
 * - RecipientData instance
 * - Closure (resolved recursively)
 * - Guard name string (resolved from config)
 * - Array of any of the above
 */
class RecipientResolver implements RecipientResolverInterface
{
    /**
     * Resolve the given target into a Collection of RecipientData DTOs.
     *
     * @param  mixed  $target  The recipients target to resolve.
     * @return Collection<int, RecipientData>
     */
    public function resolve(mixed $target): Collection
    {
        if ($target instanceof RecipientData) {
            return collect([$target]);
        }

        if ($target instanceof Closure) {
            return $this->resolve($target());
        }

        if ($target instanceof Model) {
            return collect([RecipientData::fromModel($target)]);
        }

        if ($target instanceof EloquentBuilder) {
            return $target->get()->map(fn ($model) => RecipientData::fromModel($model));
        }

        if ($target instanceof LazyCollection) {
            return collect($target->map(fn ($model) => RecipientData::fromModel($model))->all());
        }

        if (is_string($target)) {
            return $this->resolveGuard($target);
        }

        if (is_array($target)) {
            if (! empty($target) && ! array_is_list($target)) {
                return collect([RecipientData::fromModel($target)]);
            }

            $recipients = collect();

            foreach ($target as $item) {
                $recipients = $recipients->merge($this->resolve($item));
            }

            return $recipients->unique(fn (RecipientData $r) => ($r->type ?? 'array').':'.$r->id);
        }

        if ($target instanceof Collection) {
            $recipients = collect();

            foreach ($target as $item) {
                $recipients = $recipients->merge($this->resolve($item));
            }

            return $recipients->unique(fn (RecipientData $r) => ($r->type ?? 'array').':'.$r->id);
        }

        return collect();
    }

    /**
     * Resolve a guard name (or its plural/singular alias) into recipients.
     *
     * Looks up the guard in `notification-system.guards` config and loads
     * all models for the matching guard.
     *
     * @param  string  $guard  The guard name to resolve (e.g., 'students', 'admin').
     * @return Collection<int, RecipientData>
     */
    protected function resolveGuard(string $guard): Collection
    {
        $guardsConfig = config('notification-system.guards', []);

        // Match exact or singular/plural guard name
        $normalized = rtrim(strtolower($guard), 's');

        foreach ($guardsConfig as $key => $conf) {
            if ($key === $guard || $key === $normalized || rtrim($key, 's') === $normalized) {
                $modelClass = $conf['model'] ?? null;
                if ($modelClass && class_exists($modelClass)) {
                    return $modelClass::all()->map(fn ($m) => RecipientData::fromModel($m));
                }
            }
        }

        return collect();
    }
}
