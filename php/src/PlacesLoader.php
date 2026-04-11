<?php

declare(strict_types=1);

namespace Places;

/**
 * Loads place data from various file formats (JSON, SQL, TXT).
 */
class PlacesLoader
{
    public function __construct(
        private readonly string $basePath
    ) {
    }

    /**
     * Load places from JSON file.
     * 
     * @param string $filename Filename in the data directory
     * @return list<Place>
     */
    public function loadFromJson(string $filename = 'Srbija-naseljena-mesta.json'): array
    {
        $filepath = $this->basePath . DIRECTORY_SEPARATOR . $filename;
        $content = file_get_contents($filepath);
        
        if ($content === false) {
            throw new \RuntimeException("Failed to read file: {$filepath}");
        }

        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        
        $places = [];
        foreach ($data['items'] ?? [] as $item) {
            $places[] = new Place(
                id: (string) $item['id'],
                name: (string) $item['name'],
                posta: (string) $item['posta']
            );
        }
        
        return $places;
    }

    /**
     * Load places from TXT file (format: POSTA NAME).
     * 
     * @param string $filename Filename in the data directory
     * @return list<Place>
     */
    public function loadFromTxt(string $filename = 'Srbija-naseljena-mesta.txt'): array
    {
        $filepath = $this->basePath . DIRECTORY_SEPARATOR . $filename;
        $lines = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        if ($lines === false) {
            throw new \RuntimeException("Failed to read file: {$filepath}");
        }

        $places = [];
        foreach ($lines as $idx => $line) {
            $parts = preg_split('/\s+/', trim($line), 2);
            
            if (count($parts) === 2) {
                [$posta, $name] = $parts;
                $places[] = new Place(
                    id: sprintf('%04d', $idx + 1),
                    name: $name,
                    posta: $posta
                );
            }
        }
        
        return $places;
    }

    /**
     * Load places from SQL file by parsing INSERT statements.
     * 
     * @param string $filename Filename in the data directory
     * @return list<Place>
     */
    public function loadFromSql(string $filename = 'Srbija-naseljena-mesta.sql'): array
    {
        $filepath = $this->basePath . DIRECTORY_SEPARATOR . $filename;
        $lines = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        if ($lines === false) {
            throw new \RuntimeException("Failed to read file: {$filepath}");
        }

        $places = [];
        foreach ($lines as $line) {
            if (!str_starts_with(trim($line), 'INSERT INTO')) {
                continue;
            }

            try {
                // Parse: INSERT INTO `places` (`id`, `name`, `posta`) VALUES (1, 'Ada', '24430');
                $valuesStart = stripos($line, 'VALUES') + 6;
                $valuesStr = trim(substr($line, (int) $valuesStart));
                $valuesStr = rtrim($valuesStr, ';');
                $valuesStr = trim($valuesStr, '()');
                
                // Extract values (handling quoted strings)
                $parts = $this->parseSqlValues($valuesStr);
                
                if (count($parts) >= 3) {
                    $places[] = new Place(
                        id: str_pad((string) $parts[0], 4, '0', STR_PAD_LEFT),
                        name: (string) $parts[1],
                        posta: (string) $parts[2]
                    );
                }
            } catch (\Exception) {
                continue;
            }
        }
        
        return $places;
    }

    /**
     * Parse SQL VALUES string into array of values.
     * 
     * @param string $valuesStr The VALUES portion of the SQL statement
     * @return list<string>
     */
    private function parseSqlValues(string $valuesStr): array
    {
        $parts = [];
        $current = '';
        $inQuotes = false;
        
        for ($i = 0; $i < strlen($valuesStr); $i++) {
            $char = $valuesStr[$i];
            
            if ($char === "'") {
                $inQuotes = !$inQuotes;
            } elseif ($char === ',' && !$inQuotes) {
                $parts[] = trim($current, "' ");
                $current = '';
            } else {
                $current .= $char;
            }
        }
        
        $parts[] = trim($current, "' ");
        
        return $parts;
    }
}
