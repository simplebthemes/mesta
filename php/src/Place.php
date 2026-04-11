<?php

declare(strict_types=1);

namespace Places;

/**
 * Represents a settlement/place in Serbia.
 */
class Place
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $posta
    ) {
    }

    /**
     * Convert place to string format.
     */
    public function __toString(): string
    {
        return "{$this->posta} {$this->name}";
    }

    /**
     * Convert place to array format.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'posta' => $this->posta
        ];
    }
}
