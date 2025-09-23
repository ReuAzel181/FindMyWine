import { useEffect, useMemo, useState } from 'react';
import Link from 'next/link';
import WineCard from '../components/WineCard';

export default function RecommendPage() {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [data, setData] = useState({ recommendations: [], criteria: {} });
  const [ratedWines, setRatedWines] = useState([]);
  const [past, setPast] = useState([]);

  const query = useMemo(() => {
    if (typeof window === 'undefined') return new URLSearchParams();
    return new URLSearchParams(window.location.search);
  }, []);

  useEffect(() => {
    const stored = JSON.parse(localStorage.getItem('ratedWines') || '[]');
    setRatedWines(stored);
  }, []);

  useEffect(() => {
    async function load() {
      setLoading(true);
      setError('');
      try {
        const res = await fetch(`/api/recommend?${query.toString()}`);
        const json = await res.json();
        if (!res.ok) throw new Error(json.error || 'Request failed');
        setData(json);
      } catch (e) {
        setError(String(e.message || e));
      } finally {
        setLoading(false);
      }
    }
    load();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  function handleRate(wine, rating) {
    const current = JSON.parse(localStorage.getItem('ratedWines') || '[]');
    const entry = {
      name: wine.name,
      rating,
      comment: '',
      wine,
      ts: Date.now(),
    };
    const next = [entry, ...current].slice(0, 20);
    localStorage.setItem('ratedWines', JSON.stringify(next));
    setRatedWines(next);
  }

  useEffect(() => {
    try {
      const stored = JSON.parse(localStorage.getItem('pastRecommendations') || '[]');
      setPast(stored);
    } catch (e) {
      setPast([]);
    }
  }, []);

  const criteriaSummary = useMemo(() => {
    const c = data.criteria || {};
    return [
      c.type ? `Type: ${c.type}` : null,
      c.types && c.types.length ? `Types: ${c.types.join(', ')}` : null,
      c.flavors ? `Flavors: ${c.flavors}` : null,
      c.foods ? `Foods: ${c.foods}` : null,
      c.regions && c.regions.length ? `Regions: ${c.regions.join(', ')}` : null,
      (c.priceMin || c.priceMax) ? `Price: ${c.priceMin || '—'} to ${c.priceMax || '—'}` : null,
      c.randomMode ? 'Random Mode: On' : null,
    ].filter(Boolean);
  }, [data]);

  return (
    <div className="container py-4">
      <div className="d-flex justify-content-between align-items-center mb-3">
        <h3 className="mb-0 text-brand"><i className="fas fa-search me-2"></i>Recommendations</h3>
        <Link href="/" className="btn btn-sm btn-brand"><i className="fas fa-redo me-2"></i>Try another search</Link>
      </div>

      <div className="row g-3">
        <div className="col-md-9">
          <div className="card border-0 shadow-sm site-card">
            <div className="card-header bg-transparent">
              <div className="d-flex justify-content-between align-items-center">
                <strong className="text-brand">Results</strong>
                {criteriaSummary.length > 0 && (
                  <small className="text-muted">{criteriaSummary.join(' • ')}</small>
                )}
              </div>
            </div>
            <div className="card-body">
              {loading && <div className="text-center py-4"><div className="spinner-border" role="status"></div><div className="mt-2">Finding wines…</div></div>}
              {error && <div className="alert alert-danger">{error}</div>}
              {!loading && !error && (
                <div className="row row-cols-1 row-cols-md-2 g-3">
                  {data.recommendations?.map(({ wine, score }, idx) => (
                    <div className="col" key={`${wine.name}-${idx}`}>
                      <WineCard wine={wine} score={score} onRate={handleRate} />
                    </div>
                  ))}
                  {(!data.recommendations || data.recommendations.length === 0) && (
                    <div className="col">
                      <div className="alert alert-info">No wines matched your criteria. Try adjusting filters or use Random Mode.</div>
                    </div>
                  )}
                </div>
              )}
            </div>
          </div>
        </div>

        <div className="col-md-3">
          <div className="card border-0 shadow-sm mb-3 site-card">
            <div className="card-header bg-transparent"><strong className="text-brand">Past Recommendations</strong></div>
            <div className="list-group list-group-flush">
              {past.length > 0 ? past.map((p, i) => (
                <div className="list-group-item" key={i}>
                  <small className="text-muted">{new Date(p.timestamp || p.ts || Date.now()).toLocaleString()}</small>
                  <div className="mt-1">
                    {(p.wines || []).slice(0,3).map((n, idx) => (
                      <span className="badge bg-light text-dark me-1" key={`${n}-${idx}`}>{n}</span>
                    ))}
                  </div>
                </div>
              )) : (
                <div className="list-group-item">
                  <small className="text-muted">No past recommendations yet.</small>
                </div>
              )}
            </div>
          </div>

          <div className="card border-0 shadow-sm site-card">
            <div className="card-header bg-transparent"><strong className="text-brand">Your Ratings</strong></div>
            <div className="list-group list-group-flush">
              {ratedWines.length > 0 ? ratedWines.map((r, idx) => (
                <div className="list-group-item" key={`${r.name}-${idx}`}>
                  <div className="d-flex justify-content-between align-items-center">
                    <span>{r.name}</span>
                    <span className="badge bg-warning text-dark">{r.rating}★</span>
                  </div>
                </div>
              )) : (
                <div className="list-group-item">
                  <small className="text-muted">You have not rated any wines yet.</small>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}