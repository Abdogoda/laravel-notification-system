<?php

namespace NotificationSystem\Contracts;

use Illuminate\Support\Collection;
use NotificationSystem\DTOs\RecipientData;

interface RecipientResolverInterface
{
    /**
     * Resolve given target (Model, Collection, Guard name, Query, array) into a collection of RecipientData DTOs.
     *
     * @param mixed $target
     * @return Collection<int, RecipientData>
     */
    public function resolve(mixed $target): Collection;
}
