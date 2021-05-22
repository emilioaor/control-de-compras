<?php

namespace App\Contract;

use Illuminate\Database\Eloquent\Builder;
use Webpatser\Uuid\Uuid;

trait UuidGeneratorTrait {

    public function initializeUuidGeneratorTrait()
    {
        return ! $this->uuid && $this->generateUuid();
    }

    public function generateUuid(): string
    {
        return $this->uuid = Uuid::generate()->string;
    }

    public function scopeUuid(Builder $query, string $uuid): Builder
    {
        return $query->where(sprintf('%s.uuid', $this->getTable()), $uuid);
    }
}
