import { useEffect, useState } from 'react';

export default function Wines() {
  const [wines, setWines] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    async function fetchWines() {
      try {
        const res = await fetch('/api/wines');
        const data = await res.json();
        if (!res.ok) throw new Error(data.error || 'Request failed');
        setWines(data.wines || []);
      } catch (err) {
        setError(String(err.message || err));
      } finally {
        setLoading(false);
      }
    }
    fetchWines();
  }, []);

  return (
    <div className="container py-4">
      <div className="card border-0 shadow-sm site-card">
        <div className="card-header">
          <h3 className="mb-0 section-title">All Wines</h3>
        </div>
        <div className="card-body">
          {loading && <div className="text-muted">Loading…</div>}
          {error && <div className="alert alert-danger">Error: {error}</div>}

          {!loading && !error && (
            <div className="table-responsive">
              <table className="table table-hover align-middle">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Region</th>
                    <th>Country</th>
                  </tr>
                </thead>
                <tbody>
                  {wines.map((w, idx) => (
                    <tr key={`${w.name}-${idx}`}>
                      <td>{w.name}</td>
                      <td>{w.type}</td>
                      <td>${w.price}</td>
                      <td>{w.region}</td>
                      <td>{w.country}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}