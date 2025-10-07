export default function Footer() {
  return (
    <footer className="site-footer mt-5">
      <div className="container py-5">
        <div className="row g-4">
          <div className="col-12 col-md-3">
            <div className="footer-title">SITEMAP</div>
            <ul className="list-unstyled mt-3">
              <li><a className="footer-link" href="/">Home</a></li>
              <li><a className="footer-link" href="#">About Us</a></li>
              <li><a className="footer-link" href="#">Services</a></li>
              <li><a className="footer-link" href="#">Wine Delivery</a></li>
            </ul>
          </div>

          <div className="col-12 col-md-3">
            <div className="footer-title">QUICK LINKS</div>
            <ul className="list-unstyled mt-3">
              <li><a className="footer-link" href="#">Terms &amp; Condition</a></li>
              <li><a className="footer-link" href="#">Privacy Policy</a></li>
              <li><a className="footer-link" href="/wines">Browse Wines</a></li>
              <li><a className="footer-link" href="/recommend">Recommendations</a></li>
            </ul>
          </div>

          <div className="col-12 col-md-3">
            <div className="footer-title">FOLLOW US</div>
            <div className="d-flex gap-2 mt-3">
              <a className="social-icon" href="#" aria-label="X"><i className="fab fa-x-twitter"></i></a>
              <a className="social-icon" href="#" aria-label="Facebook"><i className="fab fa-facebook-f"></i></a>
              <a className="social-icon" href="#" aria-label="Instagram"><i className="fab fa-instagram"></i></a>
              <a className="social-icon" href="#" aria-label="YouTube"><i className="fab fa-youtube"></i></a>
            </div>
          </div>

          <div className="col-12 col-md-3">
            <div className="footer-title">SUBSCRIBE TO OUR NEWSLETTER</div>
            <p className="text-muted small mt-2 mb-3">Get special offers and stay updated.</p>
            <form className="d-flex flex-column flex-sm-row gap-2">
              <input className="footer-input form-control" type="email" placeholder="Type your email here" />
              <button className="footer-submit btn" type="button">SUBMIT</button>
            </form>
          </div>
        </div>
      </div>
      <div className="footer-bottom">
        <div className="container d-flex justify-content-between align-items-center py-2">
          <div className="small">Copyright © {new Date().getFullYear()} Wine Recommender. All rights reserved.</div>
          <div className="small">Web Excellence</div>
        </div>
      </div>
    </footer>
  );
}