<?php

declare(strict_types=1);

/**
 * Basic usage example for the Places library.
 * 
 * This example demonstrates loading places data and performing basic operations.
 */

require_once __DIR__ . '/../src/autoload.php';

use Places\PlacesLoader;
use Places\PlacesCollection;

echo "=== Places Library - Basic Example ===\n\n";

// Method 1: Using the convenience function
echo "Method 1: Using loadPlaces() function\n";
echo str_repeat('-', 40) . "\n";

$collection = \Places\loadPlaces();
echo "Loaded {$collection->count()} places\n\n";

// Search for places
echo "Searching for 'Beograd':\n";
$results = $collection->search('Beograd');
echo "Found " . count($results) . " places:\n";
foreach (array_slice($results, 0, 5) as $place) {
    echo "  - {$place}\n";
}
echo "\n";

// Get place by name
echo "Get place by name 'Ada':\n";
$ada = $collection->getByName('Ada');
if ($ada !== null) {
    echo "  Found: {$ada->name} ({$ada->posta})\n";
}
echo "\n";

// Get places by postal code
echo "Get places with postal code '11000':\n";
$places11000 = $collection->getByPosta('11000');
echo "Found " . count($places11000) . " places:\n";
foreach (array_slice($places11000, 0, 5) as $place) {
    echo "  - {$place->name}\n";
}
echo "\n";

// Filter by postal code prefix
echo "Filter by postal code prefix '24':\n";
$filtered = $collection->filterByPostaPrefix('24');
echo "Found " . count($filtered) . " places with postal codes starting with '24'\n";
echo "First 5:\n";
foreach (array_slice($filtered, 0, 5) as $place) {
    echo "  - {$place}\n";
}
echo "\n";

echo str_repeat('=', 40) . "\n\n";

// Method 2: Using PlacesLoader directly
echo "Method 2: Using PlacesLoader directly\n";
echo str_repeat('-', 40) . "\n";

$dataPath = dirname(__DIR__) . '/data';
$loader = new PlacesLoader($dataPath);

// Load from different formats
echo "Loading from JSON...\n";
$jsonPlaces = $loader->loadFromJson();
echo "Loaded " . count($jsonPlaces) . " places from JSON\n";

echo "Loading from TXT...\n";
$txtPlaces = $loader->loadFromTxt();
echo "Loaded " . count($txtPlaces) . " places from TXT\n";

echo "Loading from SQL...\n";
$sqlPlaces = $loader->loadFromSql();
echo "Loaded " . count($sqlPlaces) . " places from SQL\n";

echo "\nAll formats loaded the same count: " . 
    (count($jsonPlaces) === count($txtPlaces) && count($jsonPlaces) === count($sqlPlaces) ? 'YES' : 'NO') . 
    "\n\n";

// Create collection manually
$collection2 = new PlacesCollection($jsonPlaces);
echo "Created collection with {$collection2->count()} places\n\n";

// Get all places sorted
echo "First 10 places (sorted alphabetically):\n";
$all = $collection2->getAll();
foreach (array_slice($all, 0, 10) as $place) {
    echo "  {$place->name} ({$place->posta})\n";
}
echo "\n";

echo "=== Example Complete ===\n";
