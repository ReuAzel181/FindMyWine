import fs from 'fs';
import path from 'path';
import { parse } from 'csv-parse/sync';

// Resolve to the Next app's public data folder. Avoid duplicating 'next' segment.
const dataPath = path.join(process.cwd(), 'public', 'data', 'sample_wines.csv');

export function loadWines() {
  const csv = fs.readFileSync(dataPath, 'utf8');
  const records = parse(csv, {
    columns: true,
    skip_empty_lines: true,
  });
  // Normalize numeric fields
  return records.map((r) => ({
    ...r,
    price: r.price ? parseFloat(r.price) : null,
    vintage: r.vintage,
  }));
}

export function scoreWine(wine, prefs) {
  let score = 0;
  if (prefs.type && wine.type && wine.type.toLowerCase() === prefs.type.toLowerCase()) score += 3;

  if (prefs.flavors?.length && wine.flavor_profile) {
    const fp = wine.flavor_profile.toLowerCase();
    prefs.flavors.forEach((f) => {
      if (fp.includes(f.toLowerCase())) score += 1;
    });
  }

  if (prefs.foods?.length && wine.food_pairings) {
    const foods = wine.food_pairings.toLowerCase();
    prefs.foods.forEach((f) => {
      if (foods.includes(f.toLowerCase())) score += 1;
    });
  }

  if (prefs.priceMin != null && wine.price != null && wine.price >= prefs.priceMin) score += 1;
  if (prefs.priceMax != null && wine.price != null && wine.price <= prefs.priceMax) score += 1;

  if (prefs.types?.length && wine.type && prefs.types.includes(wine.type)) score += 2;
  if (prefs.regions?.length && wine.region && prefs.regions.includes(wine.region)) score += 2;

  return score;
}

export function filterWines(wines, criteria) {
  let out = wines;
  const { priceMin, priceMax, types = [], flavors = [], foodPairings = [], regions = [] } = criteria;
  if (priceMin != null) out = out.filter((w) => w.price == null || w.price >= priceMin);
  if (priceMax != null) out = out.filter((w) => w.price == null || w.price <= priceMax);
  if (types.length) out = out.filter((w) => w.type && types.includes(w.type));
  if (regions.length) out = out.filter((w) => w.region && regions.includes(w.region));
  if (flavors.length)
    out = out.filter((w) => {
      const fp = (w.flavor_profile || '').toLowerCase();
      return flavors.some((f) => {
        const term = f.toLowerCase().trim();
        if (!term) return false;
        return fp.includes(term) || (term === 'oaky' && fp.includes('oak'));
      });
    });
  if (foodPairings.length)
    out = out.filter((w) => {
      const fp = (w.food_pairings || '').toLowerCase();
      return foodPairings.some((p) => {
        const term = p.toLowerCase().trim();
        if (!term) return false;
        return fp.includes(term) || (term === 'fish' && fp.includes('seafood'));
      });
    });
  return out;
}

export function similarWines(baseWineNames, wines, criteria) {
  const base = wines.filter((w) => baseWineNames.includes(w.name));
  const commonTypes = Array.from(new Set(base.map((w) => w.type).filter(Boolean)));
  const commonRegions = Array.from(new Set(base.map((w) => w.region).filter(Boolean)));
  const commonGrapes = Array.from(new Set(base.map((w) => w.grape_variety).filter(Boolean)));

  let out = wines.filter((w) => !baseWineNames.includes(w.name));
  const { priceMin, priceMax } = criteria;
  if (priceMin != null) out = out.filter((w) => w.price == null || w.price >= priceMin);
  if (priceMax != null) out = out.filter((w) => w.price == null || w.price <= priceMax);

  out = out.filter((w) => {
    return (
      (commonTypes.length && commonTypes.includes(w.type)) ||
      (commonRegions.length && commonRegions.includes(w.region)) ||
      (commonGrapes.length && commonGrapes.includes(w.grape_variety))
    );
  });
  return shuffle(out).slice(0, 5);
}

function shuffle(arr) {
  const a = arr.slice();
  for (let i = a.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [a[i], a[j]] = [a[j], a[i]];
  }
  return a;
}