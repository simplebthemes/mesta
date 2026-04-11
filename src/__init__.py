"""
Serbian Places Data Package

A package for working with settlement data from Serbia.
"""

from .places import Place, PlacesLoader, PlacesCollection, load_places

__all__ = ["Place", "PlacesLoader", "PlacesCollection", "load_places"]
__version__ = "1.0.0"
