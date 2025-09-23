import { useEffect, useMemo, useState } from 'react';
import Link from 'next/link';
import { getWineAttributes } from '../lib/attributes';
import MultiSelect from '../components/MultiSelect';

export default function Home() {
  const [type, setType] = useState('');
  const [types, setTypes] = useState([]);
  const [regions, setRegions] = useState([]);
  const [flavors, setFlavors] = useState([]);
  const [foods, setFoods] = useState([]);
  const [priceMin, setPriceMin] = useState('');
  const [priceMax, setPriceMax] = useState('');
  const [attributes, setAttributes] = useState({ types: [], regions: [], flavors: [], foods: [], price_range: {} });
  const [winesData, setWinesData] = useState([]);
  const [searchTypes, setSearchTypes] = useState('');
  const [searchRegions, setSearchRegions] = useState('');
  const [searchFlavors, setSearchFlavors] = useState('');
  const [searchFoods, setSearchFoods] = useState('');
  const [showMoreTypes, setShowMoreTypes] = useState(false);
  const [showMoreRegions, setShowMoreRegions] = useState(false);
  const [showMoreFlavors, setShowMoreFlavors] = useState(false);
  const [showMoreFoods, setShowMoreFoods] = useState(false);
  const [rated, setRated] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  useEffect(() => {
    // Load attributes on mount, computed client-side from API
    getWineAttributes()
      .then((attrs) => setAttributes(attrs))
      .catch(() => {});
    // Load full wines dataset for popularity and live counts
    fetch('/api/wines')
      .then((r) => r.json())
      .then((json) => setWinesData(json.wines || []))
      .catch(() => {});
    // Load rated wines from localStorage
    const stored = JSON.parse(localStorage.getItem('ratedWines') || '[]');
    setRated(stored.slice(0, 6));
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

  const filteredTypes = useMemo(() => attributes.types.filter((t) => t.toLowerCase().includes(searchTypes.toLowerCase())), [attributes.types, searchTypes]);
  const filteredRegions = useMemo(() => attributes.regions.filter((r) => r.toLowerCase().includes(searchRegions.toLowerCase())), [attributes.regions, searchRegions]);
  const filteredFlavors = useMemo(() => (attributes.flavors || []).filter((f) => f.toLowerCase().includes(searchFlavors.toLowerCase())), [attributes.flavors, searchFlavors]);
  const filteredFoods = useMemo(() => (attributes.foods || []).filter((f) => f.toLowerCase().includes(searchFoods.toLowerCase())), [attributes.foods, searchFoods]);

  const popularTypes = useMemo(() => topByCount(attributes.types, typeCounts), [attributes.types, typeCounts]);
  const popularRegions = useMemo(() => topByCount(attributes.regions, regionCounts), [attributes.regions, regionCounts]);
  const popularFlavors = useMemo(() => topByCount(attributes.flavors || [], flavorCounts), [attributes.flavors, flavorCounts]);
  const popularFoods = useMemo(() => topByCount(attributes.foods || [], foodCounts), [attributes.foods, foodCounts]);

  const resultCount = useMemo(() => {
    let out = winesData.slice();
    if (types.length) out = out.filter((w) => w.type && types.includes(w.type));
    if (regions.length) out = out.filter((w) => w.region && regions.includes(w.region));
    if (flavors.length)
      out = out.filter((w) => {
        const fp = (w.flavor_profile || '').toLowerCase();
        return flavors.some((f) => fp.includes(f.toLowerCase()));
      });
    if (foods.length)
      out = out.filter((w) => {
        const fp = (w.food_pairings || '').toLowerCase();
        return foods.some((f) => fp.includes(f.toLowerCase()));
      });
    if (priceMin) out = out.filter((w) => w.price == null || w.price >= parseFloat(priceMin));
    if (priceMax) out = out.filter((w) => w.price == null || w.price <= parseFloat(priceMax));
    return out.length;
  }, [winesData, types, regions, flavors, foods, priceMin, priceMax]);

  async function fetchRecommendations(e) {
    e.preventDefault();
    setLoading(true);
    setError('');
    try {
      const anyPref = (types && types.length) || (regions && regions.length) || (flavors && flavors.length) || (foods && foods.length) || priceMin || priceMax;
      if (!anyPref) {
        setError('Please select at least one preference or criteria.');
        return;
      }
      const params = new URLSearchParams();
      if (type) params.set('type', type);
      types.forEach((t) => params.append('types', t));
      regions.forEach((r) => params.append('regions', r));
      flavors.forEach((f) => params.append('flavors', f));
      foods.forEach((f) => params.append('foods', f));
      if (priceMin) params.set('priceMin', priceMin);
      if (priceMax) params.set('priceMax', priceMax);
      const rated = JSON.parse(localStorage.getItem('ratedWines') || '[]').map((r) => r.name).filter(Boolean);
      rated.forEach((n) => params.append('ratedWineNames', n));
      params.set('limit', '6');
      const res = await fetch(`/api/recommend?${params.toString()}`);
      const data = await res.json();
      if (!res.ok) throw new Error(data.error || 'Request failed');
      // Save criteria and recommendations to localStorage for past view
      const past = JSON.parse(localStorage.getItem('pastRecommendations') || '[]');
      const item = {
        timestamp: Date.now(),
        wines: (data.recommendations || []).map((r) => r.wine?.name || ''),
      };
      localStorage.setItem('pastRecommendations', JSON.stringify([item, ...past].slice(0, 12)));
      localStorage.setItem('lastCriteria', JSON.stringify({ types, regions, flavors, foods, priceMin, priceMax }));
      // Redirect to recommendations page
      window.location.href = `/recommend?${params.toString()}`;
    } catch (err) {
      setError(String(err.message || err));
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="container-fluid home-viewport">
      <div className="row g-3">
        <div className="col-lg-10 col-md-9">
          <div className="card border-0 shadow-sm site-card h-100">
           
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
                          <MultiSelect
                            label="Wine Types"
                            iconClass="fas fa-wine-glass-alt text-warning"
                            options={attributes.types}
                            popular={popularTypes}
                            selected={types}
                            onChange={setTypes}
                            placeholder="Search types"
                          />
                        </div>
                        <div className="mb-3">
                          <MultiSelect
                            label="Preferred Flavors"
                            iconClass="fas fa-wine-glass text-warning"
                            options={attributes.flavors || []}
                            popular={popularFlavors}
                            selected={flavors}
                            onChange={setFlavors}
                            placeholder="Search flavors"
                          />
                        </div>
                        <div className="mb-3">
                          <MultiSelect
                            label="Food Pairings"
                            iconClass="fas fa-utensils text-warning"
                            options={attributes.foods || []}
                            popular={popularFoods}
                            selected={foods}
                            onChange={setFoods}
                            placeholder="Search pairings"
                          />
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
                                <input type="number" className="form-control" value={priceMin} onChange={(e) => setPriceMin(e.target.value)} min="0" step="0.01" placeholder="Min" />
                              </div>
                            </div>
                            <div className="col-md-6">
                              <div className="input-group">
                                <span className="input-group-text">$</span>
                                <input type="number" className="form-control" value={priceMax} onChange={(e) => setPriceMax(e.target.value)} min="0" step="0.01" placeholder="Max" />
                              </div>
                            </div>
                          </div>
                
                        </div>

                        <div className="mb-3">
                          <MultiSelect
                            label="Wine Regions"
                            iconClass="fas fa-globe-europe text-warning"
                            options={attributes.regions}
                            popular={popularRegions}
                            selected={regions}
                            onChange={setRegions}
                            placeholder="Search regions"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                {error && (
                  <div className="alert alert-danger py-2" role="alert">
                    <i className="fas fa-exclamation-triangle me-2"></i>{error}
                  </div>
                )}
                <div className="d-flex justify-content-between align-items-center mt-2">
                  <div className="mini-count"><i className="fas fa-wine-glass-alt"></i> Matches: <strong>{resultCount}</strong></div>
                  <button type="submit" className="btn btn-lg btn-brand px-5" disabled={loading}>
                    <i className="fas fa-search me-2"></i>{loading ? 'Finding...' : 'Find My Perfect Wine'}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div className="col-lg-2 col-md-3 rated-sidebar">
          {rated.length > 0 ? (
            <div className="card border-0 shadow-sm h-100 site-card">
              <div className="card-header bg-transparent text-center">
                <span className="text-brand"><i className="fas fa-star text-warning me-2"></i>Your Rated Wines</span>
              </div>
              <div className="card-body p-0">
                <div className="list-group list-group-flush">
                  {rated.map((r, idx) => {
                    const type = r.wine?.type || r.type || 'wine';
                    const region = r.wine?.region || r.region || 'vineyard';
                    const price = r.wine?.price || r.price;
                    const imgSrc = `https://source.unsplash.com/72x72/?wine,bottle,${encodeURIComponent(type)},${encodeURIComponent(region)}`;
                    return (
                      <div className="list-group-item border-0 rated-item" key={`${r.name}-${idx}`}>
                        <div className="d-flex align-items-center gap-3">
                          <img className="rated-thumb" src={imgSrc} alt="wine" />
                          <div className="flex-grow-1">
                            <div className="fw-semibold">{r.name}</div>
                            <small className="text-muted d-block">{type}{price ? ` — $${price}` : ''}</small>
                            <div className="rating-stars mt-1">
                              {[1,2,3,4,5].map((i) => (
                                <i key={i} className={`fas fa-star ${i <= (r.rating || 0) ? 'text-warning' : 'text-muted'}`}></i>
                              ))}
                            </div>
                          </div>
                        </div>
                      </div>
                    );
                  })}
                </div>
              </div>
            </div>
          ) : (
            <div className="card border-0 shadow-sm h-100 site-card">
              <div className="card-header bg-transparent text-center">
                <span className="text-brand"><i className="fas fa-star text-warning me-2"></i>Your Rated Wines</span>
              </div>
              <div className="card-body d-flex flex-column justify-content-center align-items-center text-center p-3">
                <i className="fas fa-wine-glass-alt fa-2x text-brand mb-2"></i>
                <div className="text-muted small">No rated wines yet</div>
              </div>
            </div>
          )}
        </div>
      </div>
      {/* Removed extra footer elements to keep homepage within viewport height */}
    </div>
  );
}