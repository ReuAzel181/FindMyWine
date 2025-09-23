export default function WineCard({ wine, score, onRate }) {
  const type = encodeURIComponent(wine.type || 'wine');
  const region = encodeURIComponent(wine.region || 'vineyard');
  const imgUrl = `https://source.unsplash.com/featured/600x400?wine,bottle,glass,${type},${region}`;
  return (
    <div className="card h-100 border-0 shadow-sm site-card">
      <img src={imgUrl} alt={wine.name} className="card-img-top wine-thumb" />
      <div className="card-body">
        <div className="d-flex justify-content-between align-items-start">
          <div>
            <h5 className="card-title mb-1 text-brand">{wine.name}</h5>
            <small className="text-muted">{wine.type} • {wine.region}, {wine.country}</small>
          </div>
          <span className="badge bg-secondary">Score {score}</span>
        </div>
        <div className="mt-2">
          <div><strong>Price:</strong> ${wine.price}</div>
          {wine.flavor_profile && <div><strong>Flavor:</strong> {wine.flavor_profile}</div>}
          {wine.food_pairings && <div><strong>Pairs with:</strong> {wine.food_pairings}</div>}
        </div>
      </div>
      <div className="card-footer bg-transparent d-flex align-items-center justify-content-between">
        <div>
          {[1,2,3,4,5].map((r) => (
            <button key={r} className="btn btn-sm btn-outline-warning me-1" onClick={() => onRate?.(wine, r)}>
              <i className="fas fa-star"></i> {r}
            </button>
          ))}
        </div>
        <small className="text-muted">Rate this wine</small>
      </div>
    </div>
  );
}