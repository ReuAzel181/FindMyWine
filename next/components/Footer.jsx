export default function Footer() {
  return (
    <footer className="site-footer mt-5">
      <div className="container py-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
        <div className="text-muted small">© {new Date().getFullYear()} Wine Recommender</div>
        <div className="text-muted small">Drink responsibly. Please follow local regulations.</div>
      </div>
    </footer>
  );
}