// Client-safe attribute extractor: fetches wines via API and computes unique attributes
export async function getWineAttributes() {
  const res = await fetch('/api/wines');
  if (!res.ok) throw new Error('Failed to load wines for attributes');
  const { wines } = await res.json();

  const types = new Set();
  const regions = new Set();
  const countries = new Set();
  const grapes = new Set();
  const flavors = new Set();
  const foods = new Set();
  let min = Infinity;
  let max = -Infinity;

  wines.forEach((w) => {
    if (w.type) types.add(w.type);
    if (w.region) regions.add(w.region);
    if (w.country) countries.add(w.country);
    if (w.grape_variety) grapes.add(w.grape_variety);
    if (w.flavor_profile) {
      String(w.flavor_profile)
        .split(/[,;/]+/)
        .map((s) => s.trim().toLowerCase())
        .filter(Boolean)
        .forEach((t) => flavors.add(t));
    }
    if (w.food_pairings) {
      String(w.food_pairings)
        .split(/[,;/]+/)
        .map((s) => s.trim().toLowerCase())
        .filter(Boolean)
        .forEach((t) => foods.add(t));
    }
    const price = typeof w.price === 'number' ? w.price : (w.price ? parseFloat(w.price) : null);
    if (typeof price === 'number' && !Number.isNaN(price)) {
      min = Math.min(min, price);
      max = Math.max(max, price);
    }
  });

  if (!isFinite(min)) min = null;
  if (!isFinite(max)) max = null;

  return {
    types: Array.from(types).filter(Boolean).sort(),
    regions: Array.from(regions).filter(Boolean).sort(),
    countries: Array.from(countries).filter(Boolean).sort(),
    grapes: Array.from(grapes).filter(Boolean).sort(),
    flavors: Array.from(flavors).filter(Boolean).sort(),
    foods: Array.from(foods).filter(Boolean).sort(),
    price_range: { min, max },
  };
}