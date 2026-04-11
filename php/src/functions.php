<?php

declare(strict_types=1);

namespace Places;

/**
 * Convenience function to load default places data.
 * 
 * @param string|null $dataPath Optional custom data path
 * @return PlacesCollection
 */
function loadPlaces(?string $dataPath = null): PlacesCollection
{
    if ($dataPath === null) {
        $dataPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'data';
    }
    
    $loader = new PlacesLoader($dataPath);
    $places = $loader->loadFromJson();
    
    return new PlacesCollection($places);
}
