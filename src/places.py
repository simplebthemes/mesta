"""
Places data module for Serbian settlements.

This module provides utilities for loading and working with place data
from various formats (JSON, SQL, TXT).
"""

import json
import os
from dataclasses import dataclass
from pathlib import Path
from typing import List, Optional


@dataclass
class Place:
    """Represents a settlement/place in Serbia."""
    
    id: str
    name: str
    posta: str
    
    def __str__(self) -> str:
        return f"{self.posta} {self.name}"
    
    def to_dict(self) -> dict:
        """Convert place to dictionary format."""
        return {
            "id": self.id,
            "name": self.name,
            "posta": self.posta
        }


class PlacesLoader:
    """Loads place data from various file formats."""
    
    def __init__(self, base_path: Optional[str] = None):
        """Initialize loader with optional base path for data files."""
        if base_path is None:
            base_path = Path(__file__).parent.parent / "data"
        self.base_path = Path(base_path)
    
    def load_from_json(self, filename: str = "Srbija-naseljena-mesta.json") -> List[Place]:
        """Load places from JSON file."""
        filepath = self.base_path / filename
        with open(filepath, 'r', encoding='utf-8') as f:
            data = json.load(f)
        
        places = []
        for item in data.get("items", []):
            places.append(Place(
                id=item["id"],
                name=item["name"],
                posta=item["posta"]
            ))
        return places
    
    def load_from_txt(self, filename: str = "Srbija-naseljena-mesta.txt") -> List[Place]:
        """Load places from TXT file (format: POSTA NAME)."""
        filepath = self.base_path / filename
        places = []
        with open(filepath, 'r', encoding='utf-8') as f:
            for idx, line in enumerate(f, start=1):
                line = line.strip()
                if not line:
                    continue
                
                parts = line.split(maxsplit=1)
                if len(parts) == 2:
                    posta, name = parts
                    places.append(Place(
                        id=f"{idx:04d}",
                        name=name,
                        posta=posta
                    ))
        return places
    
    def load_from_sql(self, filename: str = "Srbija-naseljena-mesta.sql") -> List[Place]:
        """Load places from SQL file by parsing INSERT statements."""
        filepath = self.base_path / filename
        places = []
        with open(filepath, 'r', encoding='utf-8') as f:
            for line in f:
                if line.startswith("INSERT INTO"):
                    # Parse: INSERT INTO `places` (`id`, `name`, `posta`) VALUES (1, 'Ada', '24430');
                    try:
                        values_start = line.find("VALUES") + 6
                        values_str = line[values_start:].strip().rstrip(';').strip('()')
                        
                        # Extract values (handling quoted strings)
                        parts = []
                        current = ""
                        in_quotes = False
                        for char in values_str:
                            if char == "'":
                                in_quotes = not in_quotes
                            elif char == ',' and not in_quotes:
                                parts.append(current.strip().strip("'"))
                                current = ""
                            else:
                                current += char
                        parts.append(current.strip().strip("'"))
                        
                        if len(parts) >= 3:
                            places.append(Place(
                                id=parts[0].zfill(4),
                                name=parts[1],
                                posta=parts[2]
                            ))
                    except Exception:
                        continue
        return places


class PlacesCollection:
    """Collection of places with search and filter capabilities."""
    
    def __init__(self, places: List[Place]):
        self.places = places
        self._by_name = {place.name.lower(): place for place in places}
        self._by_posta = {}
        for place in places:
            if place.posta not in self._by_posta:
                self._by_posta[place.posta] = []
            self._by_posta[place.posta].append(place)
    
    def get_by_name(self, name: str) -> Optional[Place]:
        """Get place by name (case-insensitive)."""
        return self._by_name.get(name.lower())
    
    def get_by_posta(self, posta: str) -> List[Place]:
        """Get all places with given postal code."""
        return self._by_posta.get(posta, [])
    
    def search(self, query: str) -> List[Place]:
        """Search places by name (case-insensitive partial match)."""
        query_lower = query.lower()
        return [p for p in self.places if query_lower in p.name.lower()]
    
    def filter_by_posta_prefix(self, prefix: str) -> List[Place]:
        """Filter places by postal code prefix."""
        return [p for p in self.places if p.posta.startswith(prefix)]
    
    def count(self) -> int:
        """Return total number of places."""
        return len(self.places)
    
    def get_all(self) -> List[Place]:
        """Return all places sorted by name."""
        return sorted(self.places, key=lambda p: p.name.lower())


def load_places() -> PlacesCollection:
    """Convenience function to load default places data."""
    loader = PlacesLoader()
    places = loader.load_from_json()
    return PlacesCollection(places)


if __name__ == "__main__":
    # Example usage
    collection = load_places()
    print(f"Loaded {collection.count()} places")
    
    # Search example
    results = collection.search("Beograd")
    print(f"\nPlaces matching 'Beograd': {len(results)}")
    for place in results[:5]:
        print(f"  {place}")
    
    # Postal code example
    places_11000 = collection.get_by_posta("11000")
    print(f"\nPlaces with postal code 11000: {len(places_11000)}")
