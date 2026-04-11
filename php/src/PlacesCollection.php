<?php

declare(strict_types=1);

namespace Places;

/**
 * Collection of places with search and filter capabilities.
 */
class PlacesCollection
{
    /** @var list<Place> */
    private array $places;

    /** @var array<string, Place> Map of lowercase name => place */
    private array $byName;

    /** @var array<string, list<Place>> Map of postal code => places */
    private array $byPosta;

    /**
     * @param list<Place> $places
     */
    public function __construct(array $places)
    {
        $this->places = $places;
        
        // Build index by name (case-insensitive)
        $this->byName = [];
        foreach ($places as $place) {
            $this->byName[strtolower($place->name)] = $place;
        }
        
        // Build index by postal code
        $this->byPosta = [];
        foreach ($places as $place) {
            if (!isset($this->byPosta[$place->posta])) {
                $this->byPosta[$place->posta] = [];
            }
            $this->byPosta[$place->posta][] = $place;
        }
    }

    /**
     * Get place by name (case-insensitive exact match).
     */
    public function getByName(string $name): ?Place
    {
        return $this->byName[strtolower($name)] ?? null;
    }

    /**
     * Get all places with given postal code.
     * 
     * @return list<Place>
     */
    public function getByPosta(string $posta): array
    {
        return $this->byPosta[$posta] ?? [];
    }

    /**
     * Search places by name (case-insensitive partial match).
     * 
     * @return list<Place>
     */
    public function search(string $query): array
    {
        $queryLower = strtolower($query);
        return array_filter(
            $this->places,
            fn(Place $p): bool => str_contains(strtolower($p->name), $queryLower)
        );
    }

    /**
     * Filter places by postal code prefix.
     * 
     * @return list<Place>
     */
    public function filterByPostaPrefix(string $prefix): array
    {
        return array_filter(
            $this->places,
            fn(Place $p): bool => str_starts_with($p->posta, $prefix)
        );
    }

    /**
     * Return total number of places.
     */
    public function count(): int
    {
        return count($this->places);
    }

    /**
     * Return all places sorted by name.
     * 
     * @return list<Place>
     */
    public function getAll(): array
    {
        $sorted = $this->places;
        usort($sorted, fn(Place $a, Place $b): int => 
            strcasecmp($a->name, $b->name)
        );
        return $sorted;
    }

    /**
     * Get first N places from the collection.
     * 
     * @return list<Place>
     */
    public function take(int $limit): array
    {
        return array_slice($this->places, 0, $limit);
    }
}
