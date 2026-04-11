<?php

declare(strict_types=1);

/**
 * Advanced usage examples for the Places library.
 * 
 * This example demonstrates more advanced features and use cases.
 */

require_once __DIR__ . '/../src/autoload.php';

use Places\loadPlaces;
use Places\Place;
use Places\PlacesCollection;

echo "=== Places Library - Advanced Examples ===\n\n";

$collection = loadPlaces();

// Example 1: Chaining operations
echo "Example 1: Combining search and filter\n";
echo str_repeat('-', 40) . "\n";

// Find all places with 'Novi' in name and postal code starting with '21'
$searchResults = $collection->search('Novi');
$filtered = array_filter($searchResults, fn(Place $p): bool => str_starts_with($p->posta, '21'));

echo "Places with 'Novi' in name and postal code starting with '21':\n";
foreach ($filtered as $place) {
    echo "  - {$place->name} ({$place->posta})\n";
}
echo "\n";

// Example 2: Grouping by postal code prefix
echo "Example 2: Grouping places by postal code prefix\n";
echo str_repeat('-', 40) . "\n";

$allPlaces = $collection->getAll();
$grouped = [];

foreach ($allPlaces as $place) {
    $prefix = substr($place->posta, 0, 2);
    if (!isset($grouped[$prefix])) {
        $grouped[$prefix] = 0;
    }
    $grouped[$prefix]++;
}

arsort($grouped);
echo "Top 5 postal code regions by place count:\n";
$counter = 0;
foreach ($grouped as $prefix => $count) {
    if (++$counter > 5) break;
    echo "  Region {$prefix}xx: {$count} places\n";
}
echo "\n";

// Example 3: Finding unique postal codes
echo "Example 3: Counting unique postal codes\n";
echo str_repeat('-', 40) . "\n";

$uniquePostaCodes = array_unique(array_map(fn(Place $p): string => $p->posta, $allPlaces));
echo "Total places: " . count($allPlaces) . "\n";
echo "Unique postal codes: " . count($uniquePostaCodes) . "\n";
echo "Average places per postal code: " . round(count($allPlaces) / count($uniquePostaCodes), 2) . "\n\n";

// Example 4: Search with multiple queries
echo "Example 4: Multi-criteria search\n";
echo str_repeat('-', 40) . "\n";

$queries = ['Beograd', 'Novi', 'Niš', 'Kragujevac'];
echo "Searching for major cities:\n";

foreach ($queries as $query) {
    $results = $collection->search($query);
    if (count($results) > 0) {
        $postas = array_unique(array_map(fn(Place $p): string => $p->posta, $results));
        echo "  {$query}: " . count($results) . " places, " . count($postas) . " postal codes\n";
        foreach (array_slice($results, 0, 3) as $place) {
            echo "    - {$place}\n";
        }
    }
}
echo "\n";

// Example 5: Working with Place objects
echo "Example 5: Place object methods\n";
echo str_repeat('-', 40) . "\n";

$place = $collection->getByName('Ada');
if ($place !== null) {
    echo "Place details:\n";
    echo "  ID: {$place->id}\n";
    echo "  Name: {$place->name}\n";
    echo "  Postal Code: {$place->posta}\n";
    echo "  String format: {$place}\n";
    echo "  Array format: " . json_encode($place->toArray(), JSON_UNESCAPED_UNICODE) . "\n";
}
echo "\n";

// Example 6: Pagination-like behavior
echo "Example 6: Pagination example\n";
echo str_repeat('-', 40) . "\n";

$perPage = 10;
$page = 3;
$offset = ($page - 1) * $perPage;

$allSorted = $collection->getAll();
$pageItems = array_slice($allSorted, $offset, $perPage);

echo "Page {$page} (showing items " . ($offset + 1) . "-" . ($offset + count($pageItems)) . "):\n";
foreach ($pageItems as $place) {
    echo "  {$place->name}\n";
}
echo "\nTotal pages: " . ceil(count($allSorted) / $perPage) . "\n\n";

// Example 7: Statistics
echo "Example 7: Basic statistics\n";
echo str_repeat('-', 40) . "\n";

$postaLengths = array_map(fn(Place $p): int => strlen($p->posta), $allPlaces);
$nameLengths = array_map(fn(Place $p): int => mb_strlen($p->name), $allPlaces);

echo "Postal code length - Min: " . min($postaLengths) . ", Max: " . max($postaLengths) . "\n";
echo "Name length - Min: " . min($nameLengths) . ", Max: " . max($nameLengths) . ", Avg: " . round(array_sum($nameLengths) / count($nameLengths), 2) . "\n";

// Find longest and shortest names
$sortedByLength = $allPlaces;
usort($sortedByLength, fn(Place $a, Place $b): int => mb_strlen($b->name) - mb_strlen($a->name));

echo "\nLongest place name: {$sortedByLength[0]->name} (" . mb_strlen($sortedByLength[0]->name) . " chars)\n";
echo "Shortest place name: {$sortedByLength[count($sortedByLength) - 1]->name} (" . mb_strlen($sortedByLength[count($sortedByLength) - 1]->name) . " chars)\n";
echo "\n";

echo "=== Advanced Examples Complete ===\n";
