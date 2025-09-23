import { loadWines, scoreWine, filterWines, similarWines } from '../../lib/wines';

export default function handler(req, res) {
  try {
    const { type, flavors, foods, priceMin, priceMax, limit, types, regions, randomMode, ratedWineNames } = req.query;

    const prefs = {
      type: type || null,
      flavors: Array.isArray(flavors)
        ? flavors
        : typeof flavors === 'string' && flavors.length
        ? flavors.split(',').map((s) => s.trim()).filter(Boolean)
        : [],
      foods: Array.isArray(foods)
        ? foods
        : typeof foods === 'string' && foods.length
        ? foods.split(',').map((s) => s.trim()).filter(Boolean)
        : [],
      priceMin: priceMin ? parseFloat(priceMin) : null,
      priceMax: priceMax ? parseFloat(priceMax) : null,
      types: Array.isArray(types)
        ? types
        : typeof types === 'string' && types.length
        ? types.split(',').map((s) => s.trim()).filter(Boolean)
        : [],
      regions: Array.isArray(regions)
        ? regions
        : typeof regions === 'string' && regions.length
        ? regions.split(',').map((s) => s.trim()).filter(Boolean)
        : [],
    };

    const wines = loadWines();
    const isRandom = randomMode === '1' || randomMode === 'true';
    let candidates = wines;
    if (!isRandom) {
      candidates = filterWines(wines, {
        priceMin: prefs.priceMin,
        priceMax: prefs.priceMax,
        types: prefs.types,
        flavors: prefs.flavors,
        foodPairings: prefs.foods,
        regions: prefs.regions,
      });
    }

    let preselected = candidates;
    if (!isRandom && ratedWineNames) {
      const names = Array.isArray(ratedWineNames)
        ? ratedWineNames
        : typeof ratedWineNames === 'string' && ratedWineNames.length
        ? ratedWineNames.split(',').map((s) => s.trim()).filter(Boolean)
        : [];
      if (names.length) {
        const similar = similarWines(names, wines, {
          priceMin: prefs.priceMin,
          priceMax: prefs.priceMax,
        });
        preselected = [...similar, ...candidates.filter((c) => !similar.some((s) => s.name === c.name))];
      }
    }

    const scored = preselected
      .map((w) => ({ wine: w, score: scoreWine(w, prefs) }))
      .sort((a, b) => b.score - a.score);

    const n = limit ? parseInt(limit, 10) : 10;
    res.status(200).json({ recommendations: scored.slice(0, n), criteria: prefs });
  } catch (err) {
    res.status(500).json({ error: 'Failed to recommend wines', details: String(err) });
  }
}