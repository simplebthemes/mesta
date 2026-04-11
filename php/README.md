# Places Serbia - PHP Library

PHP library for working with Serbian settlements data. Provides utilities for loading and searching place data from JSON, SQL, and TXT formats.

## Requirements

- PHP 8.0 or higher
- Composer (recommended)

## Installation

### Using Composer

```bash
cd php
composer install
```

### Manual Installation

1. Copy the `src` directory to your project
2. Include the autoloader in your code:

```php
require_once 'path/to/src/autoload.php';
```

## Usage

### Basic Example

```php
<?php

require_once 'vendor/autoload.php';

use Places\loadPlaces;

// Load places from default data source
$collection = loadPlaces();

echo "Loaded {$collection->count()} places\n";

// Search for places
$results = $collection->search('Beograd');
foreach ($results as $place) {
    echo "{$place->name} ({$place->posta})\n";
}

// Get place by name
$place = $collection->getByName('Ada');
if ($place !== null) {
    echo "Found: {$place}\n";
}

// Get places by postal code
$places = $collection->getByPosta('11000');
echo "Places with postal code 11000: " . count($places) . "\n";

// Filter by postal code prefix
$filtered = $collection->filterByPostaPrefix('24');
echo "Places with postal codes starting with '24': " . count($filtered) . "\n";
```

### Loading from Different Formats

```php
<?php

use Places\PlacesLoader;
use Places\PlacesCollection;

$dataPath = '/path/to/data';
$loader = new PlacesLoader($dataPath);

// Load from JSON
$jsonPlaces = $loader->loadFromJson('Srbija-naseljena-mesta.json');

// Load from TXT
$txtPlaces = $loader->loadFromTxt('Srbija-naseljena-mesta.txt');

// Load from SQL
$sqlPlaces = $loader->loadFromSql('Srbija-naseljena-mesta.sql');

// Create collection
$collection = new PlacesCollection($jsonPlaces);
```

### Working with Place Objects

```php
<?php

use Places\Place;

$place = new Place('0001', 'Ada', '24430');

// Access properties (readonly)
echo $place->id;      // '0001'
echo $place->name;    // 'Ada'
echo $place->posta;   // '24430'

// String representation
echo (string) $place; // '24430 Ada'

// Convert to array
$data = $place->toArray();
// ['id' => '0001', 'name' => 'Ada', 'posta' => '24430']
```

## API Reference

### Place Class

Represents a settlement/place in Serbia.

**Properties:**
- `string $id` - Place ID (readonly)
- `string $name` - Place name (readonly)
- `string $posta` - Postal code (readonly)

**Methods:**
- `__toString(): string` - Returns formatted string "POSTA NAME"
- `toArray(): array` - Converts place to associative array

### PlacesLoader Class

Loads place data from various file formats.

**Constructor:**
- `__construct(string $basePath)` - Initialize with data directory path

**Methods:**
- `loadFromJson(string $filename): array<Place>` - Load from JSON file
- `loadFromTxt(string $filename): array<Place>` - Load from TXT file (format: POSTA NAME)
- `loadFromSql(string $filename): array<Place>` - Load from SQL INSERT statements

### PlacesCollection Class

Collection of places with search and filter capabilities.

**Constructor:**
- `__construct(array<Place> $places)` - Initialize with array of places

**Methods:**
- `count(): int` - Return total number of places
- `getByName(string $name): ?Place` - Get place by exact name (case-insensitive)
- `getByPosta(string $posta): array<Place>` - Get all places with given postal code
- `search(string $query): array<Place>` - Search by name (case-insensitive partial match)
- `filterByPostaPrefix(string $prefix): array<Place>` - Filter by postal code prefix
- `getAll(): array<Place>` - Get all places sorted by name
- `take(int $limit): array<Place>` - Get first N places

### Helper Function

- `loadPlaces(?string $dataPath = null): PlacesCollection` - Convenience function to load default data

## Running Examples

```bash
# Basic usage example
php examples/basic_usage.php

# Advanced usage example
php examples/advanced_usage.php
```

## Running Tests

```bash
# Install dependencies
composer install

# Run tests
composer test
# or
./vendor/bin/phpunit
```

## Project Structure

```
php/
├── src/
│   ├── autoload.php      # Autoloader
│   ├── Place.php         # Place class
│   ├── PlacesLoader.php  # Data loader
│   ├── PlacesCollection.php  # Collection class
│   └── functions.php     # Helper functions
├── tests/
│   ├── bootstrap.php     # PHPUnit bootstrap
│   ├── PlaceTest.php     # Place tests
│   ├── PlacesLoaderTest.php  # Loader tests
│   └── PlacesCollectionTest.php  # Collection tests
├── examples/
│   ├── basic_usage.php   # Basic examples
│   └── advanced_usage.php # Advanced examples
├── composer.json         # Composer configuration
└── phpunit.xml          # PHPUnit configuration
```

## License

MIT License
