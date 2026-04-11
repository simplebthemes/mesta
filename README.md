# Serbian Places Data

A refactored Python package for working with settlement data from Serbia.

## Project Structure

```
/workspace
├── README.md           # This file
├── LICENSE             # License information
├── src/                # Source code
│   ├── __init__.py    # Package initialization
│   └── places.py      # Main module with Place, PlacesLoader, and PlacesCollection
├── tests/              # Test suite
│   └── test_places.py # Unit tests
└── data/               # Data files
    ├── Srbija-naseljena-mesta.json
    ├── Srbija-naseljena-mesta.sql
    └── Srbija-naseljena-mesta.txt
```

## Installation

No external dependencies required. Uses Python 3 standard library only.

## Usage

### Basic Usage

```python
from src.places import load_places

# Load all places
collection = load_places()
print(f"Total places: {collection.count()}")
```

### Search Places

```python
from src.places import load_places

collection = load_places()

# Search by name (partial match)
results = collection.search("Beograd")
for place in results:
    print(place)

# Get by exact name
place = collection.get_by_name("Ada")
print(place)  # Output: 24430 Ada
```

### Filter by Postal Code

```python
from src.places import load_places

collection = load_places()

# Get places by postal code
places = collection.get_by_posta("11000")

# Filter by postal code prefix
belgrade_places = collection.filter_by_posta_prefix("11")
```

### Load from Different Formats

```python
from src.places import PlacesLoader

loader = PlacesLoader()

# Load from JSON (default)
places_json = loader.load_from_json()

# Load from TXT
places_txt = loader.load_from_txt()

# Load from SQL
places_sql = loader.load_from_sql()
```

### Working with Place Objects

```python
from src.places import Place

place = Place(id="0001", name="Ada", posta="24430")

# String representation
print(str(place))  # Output: 24430 Ada

# Convert to dictionary
data = place.to_dict()  # {"id": "0001", "name": "Ada", "posta": "24430"}
```

## Running Tests

```bash
cd /workspace
python3 tests/test_places.py -v
```

## API Reference

### Classes

#### `Place`
Dataclass representing a settlement/place.

- **Attributes:**
  - `id` (str): Unique identifier
  - `name` (str): Place name
  - `posta` (str): Postal code

- **Methods:**
  - `to_dict()`: Convert to dictionary
  - `__str__()`: Returns formatted string "POSTA Name"

#### `PlacesLoader`
Loads place data from various file formats.

- **Methods:**
  - `load_from_json(filename)`: Load from JSON file
  - `load_from_txt(filename)`: Load from TXT file
  - `load_from_sql(filename)`: Load from SQL file

#### `PlacesCollection`
Collection of places with search and filter capabilities.

- **Methods:**
  - `count()`: Return total number of places
  - `get_by_name(name)`: Get place by exact name (case-insensitive)
  - `get_by_posta(posta)`: Get all places with given postal code
  - `search(query)`: Search by name (partial match, case-insensitive)
  - `filter_by_posta_prefix(prefix)`: Filter by postal code prefix
  - `get_all()`: Return all places sorted by name

## Data Files

The repository contains data about all settlements in Serbia in three formats:
- **JSON**: Structured data with metadata
- **SQL**: MySQL INSERT statements
- **TXT**: Simple text format (POSTA NAME)

All formats contain the same 1,146 settlements.
