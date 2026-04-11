<?php

declare(strict_types=1);

namespace Places\Tests;

use PHPUnit\Framework\TestCase;
use Places\PlacesCollection;
use Places\Place;
use Places\loadPlaces;

/**
 * Tests for the PlacesCollection class.
 */
class PlacesCollectionTest extends TestCase
{
    private PlacesCollection $collection;

    protected function setUp(): void
    {
        $this->collection = loadPlaces();
    }

    public function testCount(): void
    {
        $count = $this->collection->count();
        
        $this->assertGreaterThan(0, $count);
        $this->assertSame(1146, $count);
    }

    public function testGetByName(): void
    {
        $place = $this->collection->getByName('Ada');
        
        $this->assertInstanceOf(Place::class, $place);
        $this->assertSame('Ada', $place->name);
        $this->assertSame('24430', $place->posta);
    }

    public function testGetByNameCaseInsensitive(): void
    {
        $place1 = $this->collection->getByName('ada');
        $place2 = $this->collection->getByName('ADA');
        $place3 = $this->collection->getByName('Ada');
        
        $this->assertNotNull($place1);
        $this->assertNotNull($place2);
        $this->assertNotNull($place3);
        $this->assertSame($place1->id, $place2->id);
        $this->assertSame($place1->id, $place3->id);
    }

    public function testGetByNameNotFound(): void
    {
        $place = $this->collection->getByName('NonExistentPlace12345');
        
        $this->assertNull($place);
    }

    public function testGetByPosta(): void
    {
        $places = $this->collection->getByPosta('11000');
        
        $this->assertIsArray($places);
        $this->assertGreaterThan(0, count($places));
        
        foreach ($places as $place) {
            $this->assertSame('11000', $place->posta);
        }
    }

    public function testSearch(): void
    {
        $results = $this->collection->search('Beograd');
        
        $this->assertIsArray($results);
        $this->assertGreaterThan(0, count($results));
        
        foreach ($results as $place) {
            $this->assertStringContainsStringIgnoringCase('Beograd', $place->name);
        }
    }

    public function testSearchCaseInsensitive(): void
    {
        $results1 = $this->collection->search('beograd');
        $results2 = $this->collection->search('BEOGRAD');
        $results3 = $this->collection->search('Beograd');
        
        $this->assertCount(count($results1), $results2);
        $this->assertCount(count($results1), $results3);
    }

    public function testFilterByPostaPrefix(): void
    {
        $results = $this->collection->filterByPostaPrefix('11');
        
        $this->assertIsArray($results);
        $this->assertGreaterThan(0, count($results));
        
        foreach ($results as $place) {
            $this->assertStringStartsWith('11', $place->posta);
        }
    }

    public function testGetAll(): void
    {
        $all = $this->collection->getAll();
        
        $this->assertIsArray($all);
        $this->assertCount($this->collection->count(), $all);
        
        // Check if sorted
        $names = array_map(fn(Place $p): string => $p->name, $all);
        $sortedNames = $names;
        natcasesort($sortedNames);
        
        $this->assertSame(array_values($sortedNames), $names);
    }

    public function testTake(): void
    {
        $taken = $this->collection->take(5);
        
        $this->assertIsArray($taken);
        $this->assertCount(5, $taken);
    }

    public function testTakeMoreThanAvailable(): void
    {
        $count = $this->collection->count();
        $taken = $this->collection->take($count + 100);
        
        $this->assertCount($count, $taken);
    }
}
