import Link from 'next/link';
import { useState } from 'react';
import { useRouter } from 'next/router';

export default function Header() {
  const [term, setTerm] = useState('');
  const router = useRouter();
  const path = router.pathname;

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
    <header className="site-header header-expanded sticky-top">
      <div className="container-fluid d-flex align-items-center justify-content-between py-3 flex-wrap gap-3">
        <div className="d-flex align-items-center">
          <Link href="/" className="brand text-decoration-none">
            <i className="fas fa-wine-bottle me-2 text-brand"></i>
            <span className="brand-mark">Wine</span>
            <span className="brand-text">Recommender</span>
          </Link>
        </div>
        <div className="flex-grow-1 d-flex justify-content-center">
          <nav className="header-nav d-flex align-items-center gap-2">
            <Link href="/" className={`nav-link ${path === '/' ? 'active' : ''}`}>Home</Link>
            <Link href="/find" className={`nav-link ${path === '/find' ? 'active' : ''}`}>Find Wine</Link>
            <Link href="/wines" className={`nav-link ${path === '/wines' ? 'active' : ''}`}>Browse Wines</Link>
            <Link href="/recommend" className={`nav-link ${path === '/recommend' ? 'active' : ''}`}>Recommendations</Link>
          </nav>
        </div>
        <form className="searchbar d-flex ms-auto" onSubmit={onSearch}>
          <input
            className="form-control"
            placeholder="Search flavors, e.g., fruity, oaky"
            value={term}
            onChange={(e) => setTerm(e.target.value)}
          />
          <button className="btn btn-brand ms-2" type="submit">
            <i className="fas fa-search me-1"></i>Search
          </button>
        </form>
      </div>
    </header>
  );
}