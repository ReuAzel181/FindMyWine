import { useEffect, useMemo, useState } from 'react';
// Header and Footer are rendered globally via Layout in _app.jsx
import { getWineAttributes } from '../lib/attributes';
import MultiSelect from '../components/MultiSelect';

export default function Find() {
  const [types, setTypes] = useState([]);
  const [regions, setRegions] = useState([]);
  const [flavors, setFlavors] = useState([]);
  const [foods, setFoods] = useState([]);
  const [priceMin, setPriceMin] = useState('');
  const [priceMax, setPriceMax] = useState('');
  const [attributes, setAttributes] = useState({ types: [], regions: [], flavors: [], foods: [], price_range: {} });
  const [winesData, setWinesData] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  useEffect(() => {
    getWineAttributes().then((attrs) => setAttributes(attrs)).catch(() => {});
    fetch('/api/wines').then((r) => r.json()).then((json) => setWinesData(json.wines || [])).catch(() => {});
  }, []);

  const topN = 8;
  const typeCounts = useMemo(() => {
    const m = new Map();
    winesData.forEach((w) => { if (w.type) m.set(w.type, (m.get(w.type) || 0) + 1); });
    return m;
  }, [winesData]);
  const regionCounts = useMemo(() => {
    const m = new Map();
    winesData.forEach((w) => { if (w.region) m.set(w.region, (m.get(w.region) || 0) + 1); });
    return m;
  }, [winesData]);
  const flavorCounts = useMemo(() => {
    const m = new Map();
    winesData.forEach((w) => {
      const fp = (w.flavor_profile || '').toLowerCase().split(/[,;/]+/).map((s) => s.trim()).filter(Boolean);
      fp.forEach((f) => m.set(f, (m.get(f) || 0) + 1));
    });
    return m;
  }, [winesData]);
  const foodCounts = useMemo(() => {
    const m = new Map();
    winesData.forEach((w) => {
      const fp = (w.food_pairings || '').toLowerCase().split(/[,;/]+/).map((s) => s.trim()).filter(Boolean);
      fp.forEach((f) => m.set(f, (m.get(f) || 0) + 1));
    });
    return m;
  }, [winesData]);

  function topByCount(tokens, counts) {
    return tokens
      .map((t) => ({ t, c: counts.get(t) || 0 }))
      .sort((a, b) => b.c - a.c)
      .slice(0, topN)
      .map((x) => x.t);
  }

  const popularTypes = useMemo(() => topByCount(attributes.types, typeCounts), [attributes.types, typeCounts]);
  const popularRegions = useMemo(() => topByCount(attributes.regions, regionCounts), [attributes.regions, regionCounts]);
  const popularFlavors = useMemo(() => topByCount(attributes.flavors || [], flavorCounts), [attributes.flavors, flavorCounts]);
  const popularFoods = useMemo(() => topByCount(attributes.foods || [], foodCounts), [attributes.foods, foodCounts]);

  const priceError = useMemo(() => {
    if (!priceMin || !priceMax) return false;
    const min = parseFloat(priceMin);
    const max = parseFloat(priceMax);
    if (Number.isNaN(min) || Number.isNaN(max)) return false;
    return min > max;
  }, [priceMin, priceMax]);

  async function fetchRecommendations(e) {
    e.preventDefault();
    setLoading(true);
    setError('');
    try {
      if (priceError) {
        setError('Price range is invalid: Min cannot exceed Max.');
        return;
      }
      const anyPref = (types && types.length) || (regions && regions.length) || (flavors && flavors.length) || (foods && foods.length) || priceMin || priceMax;
      if (!anyPref) {
        setError('Please select at least one preference or criteria.');
        return;
      }
      const params = new URLSearchParams();
      types.forEach((t) => params.append('types', t));
      regions.forEach((r) => params.append('regions', r));
      flavors.forEach((f) => params.append('flavors', f));
      foods.forEach((f) => params.append('foods', f));
      if (priceMin) params.set('priceMin', priceMin);
      if (priceMax) params.set('priceMax', priceMax);
      params.set('limit', '6');
      window.location.href = `/recommend?${params.toString()}`;
    } catch (err) {
      setError(String(err.message || err));
    } finally {
      setLoading(false);
    }
  }

  return (
    <>
    <div className="container py-4">
      <div className="row g-3">
        <div className="col-lg-9">
          <div className="card border-0 shadow-sm site-card">
            <div className="card-header bg-transparent text-dark border-0 text-center py-2">
              <h5 className="mb-0 text-brand"><i className="fas fa-wine-bottle me-2"></i>Find Your Perfect Wine</h5>
            </div>
            <div className="card-body">
              <form onSubmit={fetchRecommendations}>
                <div className="row g-3 mb-4">
                  <div className="col-md-6">
                    <div className="card h-100 border-0 shadow-sm site-card">
                      <div className="card-header bg-transparent text-dark border-0 text-center py-2">
                        <h6 className="mb-0 text-brand"><i className="fas fa-wine-bottle me-2"></i>Wine Preferences</h6>
                      </div>
                      <div className="card-body">
                        <div className="mb-3">
                          <MultiSelect label="Wine Types" iconClass="fas fa-wine-glass-alt text-warning" options={attributes.types} popular={popularTypes} selected={types} onChange={setTypes} placeholder="Search types" />
                        </div>
                        <div className="mb-3">
                          <MultiSelect label="Preferred Flavors" iconClass="fas fa-wine-glass text-warning" options={attributes.flavors || []} popular={popularFlavors} selected={flavors} onChange={setFlavors} placeholder="Search flavors" />
                        </div>
                        <div className="mb-3">
                          <MultiSelect label="Food Pairings" iconClass="fas fa-utensils text-warning" options={attributes.foods || []} popular={popularFoods} selected={foods} onChange={setFoods} placeholder="Search pairings" />
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="col-md-6">
                    <div className="card h-100 border-0 shadow-sm site-card">
                      <div className="card-header bg-transparent text-dark border-0 text-center py-2">
                        <h6 className="mb-0 text-brand"><i className="fas fa-sliders-h me-2"></i>Additional Criteria</h6>
                      </div>
                      <div className="card-body">
                        <div className="mb-3">
                          <label className="form-label"><i className="fas fa-tag text-warning"></i> Price Range</label>
                          <div className="row g-2">
                            <div className="col-md-6">
                              <div className="input-group">
                                <span className="input-group-text">$</span>
                                <input type="number" className={`form-control ${priceError ? 'is-invalid' : ''}`} aria-invalid={priceError} value={priceMin} onChange={(e) => setPriceMin(e.target.value)} min="0" step="0.01" placeholder="Min" />
                              </div>
                            </div>
                            <div className="col-md-6">
                              <div className="input-group">
                                <span className="input-group-text">$</span>
                                <input type="number" className={`form-control ${priceError ? 'is-invalid' : ''}`} aria-invalid={priceError} value={priceMax} onChange={(e) => setPriceMax(e.target.value)} min="0" step="0.01" placeholder="Max" />
                              </div>
                            </div>
                          </div>
                          {priceError && (<div className="invalid-feedback d-block">Min price must be less than or equal to Max price.</div>)}
                        </div>

                        <div className="mb-3">
                          <MultiSelect label="Wine Regions" iconClass="fas fa-globe-europe text-warning" options={attributes.regions} popular={popularRegions} selected={regions} onChange={setRegions} placeholder="Search regions" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div className="d-flex justify-content-end">
                  <button type="submit" className="btn btn-brand" disabled={loading}>
                    <i className="fas fa-search me-2"></i>{loading ? 'Finding...' : 'Find My Perfect Wine'}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div className="col-lg-3">
          <div className="card border-0 shadow-sm site-card">
            <div className="card-header bg-transparent text-center">
              <span className="text-brand"><i className="fas fa-lightbulb text-warning me-2"></i>Tips</span>
            </div>
            <div className="card-body">
              <ul className="small text-muted mb-0">
                <li>Select a few wine types and flavors to get focused matches.</li>
                <li>Use price range to stay within budget.</li>
                <li>Pairings help tailor recommendations to your meal.</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    </>
  );
}