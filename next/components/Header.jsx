import Link from 'next/link';
import { useState } from 'react';

export default function Header() {
  const [term, setTerm] = useState('');

  function onSearch(e) {
    e.preventDefault();
    const t = term.trim();
    if (!t) return;
    const params = new URLSearchParams();
    params.set('flavors', t);
    params.set('limit', '6');
    window.location.href = `/recommend?${params.toString()}`;
  }

  return (
    <header className="site-header shadow-sm sticky-top">
      <div className="container d-flex align-items-center justify-content-between py-2">
        <div className="d-flex align-items-center">
          <Link href="/" className="brand text-decoration-none">
            <i className="fas fa-wine-bottle me-2 text-brand"></i>
            <span className="brand-mark">Wine</span>
            <span className="brand-text">Recommender</span>
          </Link>
        </div>
        <nav className="d-none d-md-flex align-items-center gap-2">
          <Link href="/" className="nav-link chip">Home</Link>
          <Link href="/wines" className="nav-link chip">Browse Wines</Link>
          <Link href="/recommend" className="nav-link chip">Recommendations</Link>
        </nav>
        <form className="searchbar d-none d-md-flex" onSubmit={onSearch}>
          <input
            className="form-control form-control-sm"
            placeholder="Search flavors, e.g., fruity, oaky"
            value={term}
            onChange={(e) => setTerm(e.target.value)}
          />
          <button className="btn btn-sm btn-brand ms-2" type="submit">
            <i className="fas fa-search me-1"></i>Search
          </button>
        </form>
      </div>
      <div className="header-subnav">
        <div className="container d-flex flex-wrap gap-2 py-2">
          <Link href="/recommend?types=Red&limit=6" className="nav-link chip">Red</Link>
          <Link href="/recommend?types=White&limit=6" className="nav-link chip">White</Link>
          <Link href="/recommend?types=Sparkling&limit=6" className="nav-link chip">Sparkling</Link>
          <Link href="/recommend?types=Rosé&limit=6" className="nav-link chip">Rosé</Link>
          <Link href="/recommend?types=Dessert&limit=6" className="nav-link chip">Dessert</Link>
          <Link href="/recommend?flavors=fruity&limit=6" className="nav-link chip">Fruity</Link>
          <Link href="/recommend?flavors=oaky&limit=6" className="nav-link chip">Oaky</Link>
          <Link href="/recommend?foods=steak&limit=6" className="nav-link chip">Steak</Link>
          <Link href="/recommend?foods=fish&limit=6" className="nav-link chip">Fish</Link>
        </div>
      </div>
    </header>
  );
}