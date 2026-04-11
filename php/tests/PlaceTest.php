<?php

declare(strict_types=1);

namespace Places\Tests;

use PHPUnit\Framework\TestCase;
use Places\Place;
use Places\PlacesLoader;
use Places\PlacesCollection;
use Places\loadPlaces;

/**
 * Tests for the Place class.
 */
class PlaceTest extends TestCase
{
    public function testConstructor(): void
    {
        $place = new Place('0001', 'Ada', '24430');
        
        $this->assertSame('0001', $place->id);
        $this->assertSame('Ada', $place->name);
        $this->assertSame('24430', $place->posta);
    }

    public function testToString(): void
    {
        $place = new Place('0001', 'Ada', '24430');
        
        $this->assertSame('24430 Ada', (string) $place);
    }

    public function testToArray(): void
    {
        $place = new Place('0001', 'Ada', '24430');
        
        $expected = [
            'id' => '0001',
            'name' => 'Ada',
            'posta' => '24430'
        ];
        
        $this->assertSame($expected, $place->toArray());
    }

    public function testReadonlyProperties(): void
    {
        $place = new Place('0001', 'Ada', '24430');
        
        // Properties should be readonly - trying to modify should cause error in PHP 8.1+
        $this->expectException(\Error::class);
        /** @phpstan-ignore-next-line */
        $place->name = 'Modified';
    }
}
