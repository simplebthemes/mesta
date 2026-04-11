"""
Tests for the places module.
"""

import unittest
import sys
from pathlib import Path

# Add src to path
sys.path.insert(0, str(Path(__file__).parent.parent / "src"))

from places import Place, PlacesLoader, PlacesCollection, load_places


class TestPlace(unittest.TestCase):
    """Tests for the Place dataclass."""
    
    def test_place_creation(self):
        place = Place(id="0001", name="Ada", posta="24430")
        self.assertEqual(place.id, "0001")
        self.assertEqual(place.name, "Ada")
        self.assertEqual(place.posta, "24430")
    
    def test_place_str(self):
        place = Place(id="0001", name="Ada", posta="24430")
        self.assertEqual(str(place), "24430 Ada")
    
    def test_place_to_dict(self):
        place = Place(id="0001", name="Ada", posta="24430")
        expected = {"id": "0001", "name": "Ada", "posta": "24430"}
        self.assertEqual(place.to_dict(), expected)


class TestPlacesLoader(unittest.TestCase):
    """Tests for the PlacesLoader class."""
    
    def setUp(self):
        self.loader = PlacesLoader()
    
    def test_load_from_json(self):
        places = self.loader.load_from_json()
        self.assertGreater(len(places), 0)
        self.assertIsInstance(places[0], Place)
    
    def test_load_from_txt(self):
        places = self.loader.load_from_txt()
        self.assertGreater(len(places), 0)
        self.assertIsInstance(places[0], Place)
    
    def test_load_from_sql(self):
        places = self.loader.load_from_sql()
        self.assertGreater(len(places), 0)
        self.assertIsInstance(places[0], Place)
    
    def test_all_formats_same_count(self):
        json_places = self.loader.load_from_json()
        txt_places = self.loader.load_from_txt()
        sql_places = self.loader.load_from_sql()
        
        # All formats should have the same number of places
        self.assertEqual(len(json_places), len(txt_places))
        self.assertEqual(len(json_places), len(sql_places))


class TestPlacesCollection(unittest.TestCase):
    """Tests for the PlacesCollection class."""
    
    def setUp(self):
        loader = PlacesLoader()
        places = loader.load_from_json()
        self.collection = PlacesCollection(places)
    
    def test_count(self):
        self.assertGreater(self.collection.count(), 0)
    
    def test_get_by_name(self):
        place = self.collection.get_by_name("Ada")
        self.assertIsNotNone(place)
        self.assertEqual(place.name, "Ada")
    
    def test_get_by_name_case_insensitive(self):
        place1 = self.collection.get_by_name("Ada")
        place2 = self.collection.get_by_name("ada")
        self.assertEqual(place1, place2)
    
    def test_search(self):
        results = self.collection.search("Beograd")
        self.assertGreater(len(results), 0)
        for place in results:
            self.assertIn("beograd", place.name.lower())
    
    def test_filter_by_posta_prefix(self):
        results = self.collection.filter_by_posta_prefix("11")
        self.assertGreater(len(results), 0)
        for place in results:
            self.assertTrue(place.posta.startswith("11"))
    
    def test_get_all_sorted(self):
        all_places = self.collection.get_all()
        names = [p.name.lower() for p in all_places]
        self.assertEqual(names, sorted(names))


if __name__ == "__main__":
    unittest.main()
