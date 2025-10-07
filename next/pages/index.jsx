import Link from 'next/link';
import { useEffect, useMemo, useRef, useState } from 'react';
// Header and Footer are rendered globally via Layout in _app.jsx

export default function Home() {
  const [wines, setWines] = useState([]);
  const [rated, setRated] = useState([]);

  // Count-up animation on visibility
  function useInView(options) {
    const ref = useRef(null);
    const [inView, setInView] = useState(false);
    useEffect(() => {
      const node = ref.current;
      if (!node) return;
      const observer = new IntersectionObserver((entries) => {
        entries.forEach((e) => {
          if (e.isIntersecting) setInView(true);
        });
      }, options || { threshold: 0.3 });
      observer.observe(node);
      return () => observer.disconnect();
    }, [options]);
    return { ref, inView };
  }

  function CountUp({ end = 0, duration = 1200 }) {
    const { ref, inView } = useInView();
    const [value, setValue] = useState(0);
    useEffect(() => {
      if (!inView) return;
      let startTime;
      const start = 0;
      const delta = Number(end) || 0;
      function step(ts) {
        if (!startTime) startTime = ts;
        const progress = Math.min((ts - startTime) / duration, 1);
        setValue(Math.floor(start + progress * delta));
        if (progress < 1) requestAnimationFrame(step);
      }
      const raf = requestAnimationFrame(step);
      return () => cancelAnimationFrame(raf);
    }, [inView, end, duration]);
    return (
      <span ref={ref}>{value}</span>
    );
  }

  useEffect(() => {
    fetch('/api/wines').then((r) => r.json()).then((j) => setWines(j.wines || [])).catch(() => {});
    const stored = JSON.parse(localStorage.getItem('ratedWines') || '[]');
    setRated(stored.slice(0, 3));
  }, []);

  const topN = 6;
  const typeCounts = useMemo(() => {
    const m = new Map();
    wines.forEach((w) => { if (w.type) m.set(w.type, (m.get(w.type) || 0) + 1); });
    return m;
  }, [wines]);
  const flavorCounts = useMemo(() => {
    const m = new Map();
    wines.forEach((w) => {
      const fp = (w.flavor_profile || '').toLowerCase().split(/[,;/]+/).map((s) => s.trim()).filter(Boolean);
      fp.forEach((f) => m.set(f, (m.get(f) || 0) + 1));
    });
    return m;
  }, [wines]);
  const foodCounts = useMemo(() => {
    const m = new Map();
    wines.forEach((w) => {
      const fp = (w.food_pairings || '').toLowerCase().split(/[,;/]+/).map((s) => s.trim()).filter(Boolean);
      fp.forEach((f) => m.set(f, (m.get(f) || 0) + 1));
    });
    return m;
  }, [wines]);
  const typesSet = useMemo(() => Array.from(new Set(wines.map((w) => w.type).filter(Boolean))), [wines]);
  const flavorsSet = useMemo(() => {
    const s = new Set();
    wines.forEach((w) => (w.flavor_profile || '').toLowerCase().split(/[,;/]+/).map((x) => x.trim()).filter(Boolean).forEach((x) => s.add(x)));
    return Array.from(s);
  }, [wines]);
  const foodsSet = useMemo(() => {
    const s = new Set();
    wines.forEach((w) => (w.food_pairings || '').toLowerCase().split(/[,;/]+/).map((x) => x.trim()).filter(Boolean).forEach((x) => s.add(x)));
    return Array.from(s);
  }, [wines]);

  function topByCount(tokens, counts) {
    return tokens
      .map((t) => ({ t, c: counts.get(t) || 0 }))
      .sort((a, b) => b.c - a.c)
      .slice(0, topN)
      .map((x) => x.t);
  }

  const popularTypes = useMemo(() => topByCount(typesSet, typeCounts), [typesSet, typeCounts]);
  const popularFlavors = useMemo(() => topByCount(flavorsSet, flavorCounts), [flavorsSet, flavorCounts]);
  const popularFoods = useMemo(() => topByCount(foodsSet, foodCounts), [foodsSet, foodCounts]);

  const stats = useMemo(() => {
    const regions = new Set(wines.map((w) => w.region).filter(Boolean));
    const countries = new Set(wines.map((w) => w.country).filter(Boolean));
    return {
      wines: wines.length,
      types: typesSet.length,
      regions: regions.size,
      countries: countries.size,
    };
  }, [wines, typesSet]);

  return (
    <>
        <section className="container py-5">
          <div className="row align-items-center gy-4">
            <div className="col-lg-6">
              <h1 className="display-5 fw-bold section-title mb-3">Find wines you’ll love</h1>
              <p className="lead text-muted mb-4">
                Discover tailored recommendations by taste, food pairings, and budget.
                Start with a quick preference selection and let us do the rest.
              </p>
              <ul className="list-unstyled text-muted small mb-4">
                <li className="mb-2"><i className="fas fa-check text-brand me-2"></i>Curated by flavor profiles, regions, and styles</li>
                <li className="mb-2"><i className="fas fa-check text-brand me-2"></i>Pair with cuisine or ingredients for perfect matches</li>
                <li className="mb-2"><i className="fas fa-check text-brand me-2"></i>Filter by price to stay on budget</li>
              </ul>
              <div className="d-flex gap-3">
                <Link href="/find" className="btn btn-brand btn-lg">
                  <i className="fas fa-search me-2"></i>Start Finding
                </Link>
                <Link href="/wines" className="btn btn-outline-secondary btn-lg">
                  Browse Wines
                </Link>
              </div>
              <div className="text-muted mt-3 small">No sign up required. Explore instantly.</div>
            </div>
            <div className="col-lg-6">
              <img
                className="img-fluid rounded-3 shadow-sm"
                src="https://images.unsplash.com/photo-1528133084100-7f7d87914512?q=80&w=1200&auto=format&fit=crop"
                alt="Wine cellar"
              />
            </div>
          </div>
        </section>

        <section className="container pb-5">
          <div className="row g-4">
            <div className="col-md-4">
              <div className="card border-0 shadow-sm site-card h-100 hover-lift fade-in">
                <div className="card-body">
                  <div className="d-flex align-items-start gap-3">
                    <i className="fas fa-wine-glass-alt text-brand fa-lg"></i>
                    <div>
                      <h6 className="mb-1">Taste-first recommendations</h6>
                      <p className="text-muted mb-2">Choose flavors you like and we’ll match wines that fit. Explore styles like bold reds, crisp whites, and sparkling options.</p>
                      <Link href="/find" className="footer-link">Start with flavors</Link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div className="col-md-4">
              <div className="card border-0 shadow-sm site-card h-100 hover-lift fade-in" style={{animationDelay:'0.08s'}}>
                <div className="card-body">
                  <div className="d-flex align-items-start gap-3">
                    <i className="fas fa-utensils text-brand fa-lg"></i>
                    <div>
                      <h6 className="mb-1">Perfect with your meal</h6>
                      <p className="text-muted mb-2">Pair by cuisine or dish for elevated dining at home. From steak nights to sushi, get reliable matches.</p>
                      <Link href="/recommend?foods=steak&limit=6" className="footer-link">See steak pairings</Link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div className="col-md-4">
              <div className="card border-0 shadow-sm site-card h-100 hover-lift fade-in" style={{animationDelay:'0.16s'}}>
                <div className="card-body">
                  <div className="d-flex align-items-start gap-3">
                    <i className="fas fa-tag text-brand fa-lg"></i>
                    <div>
                      <h6 className="mb-1">Fits your budget</h6>
                      <p className="text-muted mb-2">Set a range and explore great options without overspending. Discover quality bottles at every price point.</p>
                      <Link href="/recommend?maxPrice=25&limit=6" className="footer-link">Under $25 picks</Link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Quick start picks directly linked to recommendations */}
        <section className="container pb-5">
          <div className="card border-0 shadow-sm site-card">
            <div className="card-header bg-transparent">
              <h5 className="mb-0 section-title">Quick Start</h5>
            </div>
            <div className="card-body">
              <p className="section-subtitle text-muted">Jump straight into curated results. Tap a chip to view recommendations filtered by popular choices from our dataset.</p>
              <div className="row g-4">
                <div className="col-md-4">
                  <div className="chip-group">
                    <div className="group-header"><strong>Popular Types</strong></div>
                    <div className="chip-scroll">
                      {popularTypes.map((t) => (
                        <Link key={t} href={`/recommend?types=${encodeURIComponent(t)}&limit=6`} className="chip">{t}</Link>
                      ))}
                    </div>
                    <div className="mt-2"><Link href="/wines" className="footer-link">Explore all types</Link></div>
                  </div>
                </div>
                <div className="col-md-4">
                  <div className="chip-group">
                    <div className="group-header"><strong>Popular Flavors</strong></div>
                    <div className="chip-scroll">
                      {popularFlavors.map((f) => (
                        <Link key={f} href={`/recommend?flavors=${encodeURIComponent(f)}&limit=6`} className="chip">{f}</Link>
                      ))}
                    </div>
                    <div className="mt-2"><Link href="/find" className="footer-link">Choose your flavor profile</Link></div>
                  </div>
                </div>
                <div className="col-md-4">
                  <div className="chip-group">
                    <div className="group-header"><strong>Popular Pairings</strong></div>
                    <div className="chip-scroll">
                      {popularFoods.map((p) => (
                        <Link key={p} href={`/recommend?foods=${encodeURIComponent(p)}&limit=6`} className="chip">{p}</Link>
                      ))}
                    </div>
                    <div className="mt-2"><Link href="/recommend" className="footer-link">Browse pairing ideas</Link></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Coverage stats derived from dataset */}
        <section className="container pb-5">
          <div className="card border-0 shadow-sm site-card">
            <div className="card-body">
              <div className="row text-center g-4">
                <div className="col-6 col-md-3">
                  <div className="fw-bold display-6 text-brand stat-tile"><CountUp end={stats.wines || 0} duration={1200} /></div>
                  <div className="text-muted">Wines</div>
                  <div className="small text-muted">Continuously updated catalog across many vintages</div>
                </div>
                <div className="col-6 col-md-3">
                  <div className="fw-bold display-6 text-brand stat-tile"><CountUp end={stats.types || 0} duration={1200} /></div>
                  <div className="text-muted">Types</div>
                  <div className="small text-muted">From robust reds to refreshing whites and rosé</div>
                </div>
                <div className="col-6 col-md-3">
                  <div className="fw-bold display-6 text-brand stat-tile"><CountUp end={stats.regions || 0} duration={1200} /></div>
                  <div className="text-muted">Regions</div>
                  <div className="small text-muted">Discover terroirs spanning old and new world</div>
                </div>
                <div className="col-6 col-md-3">
                  <div className="fw-bold display-6 text-brand stat-tile"><CountUp end={stats.countries || 0} duration={1200} /></div>
                  <div className="text-muted">Countries</div>
                  <div className="small text-muted">Global selections curated for diverse palates</div>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Your rated wines preview */}
        <section className="container pb-5">
          <div className="card border-0 shadow-sm site-card">
            <div className="card-header bg-transparent">
              <h5 className="mb-0 section-title">Your Rated Wines</h5>
            </div>
            <div className="card-body">
              {rated.length ? (
                <div className="row g-3">
                  {rated.map((r, idx) => {
                    const type = r.wine?.type || r.type || 'wine';
                    const region = r.wine?.region || r.region || 'vineyard';
                    const price = r.wine?.price || r.price;
                    const imgSrc = `https://source.unsplash.com/300x200/?wine,bottle,${encodeURIComponent(type)},${encodeURIComponent(region)}`;
                    return (
                      <div className="col-md-4" key={`${r.name}-${idx}`}>
                        <div className="card h-100 border-0 shadow-sm site-card hover-lift fade-in">
                          <img className="card-img-top" src={imgSrc} alt="wine" />
                          <div className="card-body">
                            <div className="fw-semibold mb-1">{r.name}</div>
                            <small className="text-muted d-block">{type}{price ? ` — $${price}` : ''}</small>
                            <div className="rating-stars mt-2">
                              {[1,2,3,4,5].map((i) => (
                                <i key={i} className={`fas fa-star ${i <= (r.rating || 0) ? 'text-warning' : 'text-muted'}`}></i>
                              ))}
                            </div>
                            <div className="mt-2"><Link href={`/wines?type=${encodeURIComponent(type)}`} className="footer-link">See similar wines</Link></div>
                          </div>
                        </div>
                      </div>
                    );
                  })}
                </div>
              ) : (
                <div className="text-muted">No ratings yet. Try <Link href="/find">finding a wine</Link> and rate your picks. Your top three recent ratings will appear here for quick access.</div>
              )}
            </div>
          </div>
        </section>
    </>
  );
}