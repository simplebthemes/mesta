<?php

declare(strict_types=1);

namespace Places\Tests;

use PHPUnit\Framework\TestCase;
use Places\PlacesLoader;

/**
 * Tests for the PlacesLoader class.
 */
class PlacesLoaderTest extends TestCase
{
    private string $dataPath;

    protected function setUp(): void
    {
        $this->dataPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'data';
    }

    public function testLoadFromJson(): void
    {
        $loader = new PlacesLoader($this->dataPath);
        $places = $loader->loadFromJson();
        
        $this->assertIsArray($places);
        $this->assertGreaterThan(0, count($places));
        $this->assertInstanceOf(\Places\Place::class, $places[0]);
        
        // Check first place
        $this->assertSame('0001', $places[0]->id);
        $this->assertSame('Ada', $places[0]->name);
        $this->assertSame('24430', $places[0]->posta);
    }

    public function testLoadFromTxt(): void
    {
        $loader = new PlacesLoader($this->dataPath);
        $places = $loader->loadFromTxt();
        
        $this->assertIsArray($places);
        $this->assertGreaterThan(0, count($places));
        $this->assertInstanceOf(\Places\Place::class, $places[0]);
    }

    public function testLoadFromSql(): void
    {
        $loader = new PlacesLoader($this->dataPath);
        $places = $loader->loadFromSql();
        
        $this->assertIsArray($places);
        $this->assertGreaterThan(0, count($places));
        $this->assertInstanceOf(\Places\Place::class, $places[0]);
    }

    public function testAllFormatsLoadSameCount(): void
    {
        $loader = new PlacesLoader($this->dataPath);
        
        $jsonCount = count($loader->loadFromJson());
        $txtCount = count($loader->loadFromTxt());
        $sqlCount = count($loader->loadFromSql());
        
        $this->assertSame($jsonCount, $txtCount, 'JSON and TXT should load same number of places');
        $this->assertSame($jsonCount, $sqlCount, 'JSON and SQL should load same number of places');
    }

    public function testLoadNonExistentFile(): void
    {
        $loader = new PlacesLoader($this->dataPath);
        
        $this->expectException(\RuntimeException::class);
        $loader->loadFromJson('nonexistent.json');
    }
}
