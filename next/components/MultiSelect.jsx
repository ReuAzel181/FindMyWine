import { useEffect, useMemo, useRef, useState } from 'react';

export default function MultiSelect({
  label,
  iconClass,
  options = [],
  selected = [],
  onChange,
  placeholder = 'Search...',
  popular = [],
}) {
  const [open, setOpen] = useState(false);
  const [term, setTerm] = useState('');
  const ref = useRef(null);

  useEffect(() => {
    function onDocClick(e) {
      if (!ref.current) return;
      if (!ref.current.contains(e.target)) setOpen(false);
    }
    document.addEventListener('mousedown', onDocClick);
    return () => document.removeEventListener('mousedown', onDocClick);
  }, []);

  const filtered = useMemo(() => {
    const t = term.trim().toLowerCase();
    return options.filter((o) => o.toLowerCase().includes(t));
  }, [options, term]);

  const toggleItem = (item) => {
    const active = selected.includes(item);
    const next = active ? selected.filter((x) => x !== item) : [...selected, item];
    onChange?.(next);
  };

  const clearAll = () => onChange?.([]);

  const addPopular = (n = 3) => {
    const picks = (popular || []).slice(0, n);
    const next = Array.from(new Set([...(selected || []), ...picks]));
    onChange?.(next);
  };

  const summaryText = selected.length
    ? `${selected.slice(0, 3).join(', ')}${selected.length > 3 ? '…' : ''}`
    : (popular && popular.length
        ? `Popular: ${popular.slice(0, 3).join(', ')}`
        : 'None');

  return (
    <div className="multiselect" ref={ref}>
      <button type="button" className="ms-control" onClick={() => setOpen((v) => !v)}>
        <span className="d-flex align-items-center">
          {iconClass && <i className={`${iconClass} me-2`}></i>}
          <span className="ms-label">{label}</span>
        </span>
        <span className="text-muted small ms-summary">{summaryText}</span>
        <i className={`fas fa-chevron-${open ? 'up' : 'down'} ms-2 text-muted`}></i>
      </button>
      {/* Quick picks: show top popular as clickable chips when nothing selected */}
      {(!selected || selected.length === 0) && popular?.length > 0 && (
        <div className="ms-quick mt-2">
          <span className="text-muted small">Quick picks:</span>
          {popular.slice(0, 3).map((p) => (
            <button type="button" key={`qp-${p}`} className="chip" onClick={() => toggleItem(p)}>{p}</button>
          ))}
          {popular.length > 3 && (
            <button type="button" className="btn btn-link btn-sm p-0 ms-1" onClick={() => setOpen(true)}>See more</button>
          )}
          <div className="ms-auto"></div>
          <button type="button" className="btn btn-sm btn-outline-secondary" onClick={() => addPopular(3)}>Add All</button>
        </div>
      )}
      {selected.length > 0 && (
        <div className="ms-selected mt-2">
          {selected.map((s) => (
            <span key={s} className="chip active me-2 mb-2" onClick={() => toggleItem(s)}>{s}</span>
          ))}
        </div>
      )}
      {open && (
        <div className="ms-panel shadow-sm">
          <div className="d-flex align-items-center gap-2 mb-2">
            <input className="form-control form-control-sm" placeholder={placeholder} value={term} onChange={(e) => setTerm(e.target.value)} />
            <button type="button" className="btn btn-sm btn-outline-secondary" onClick={clearAll}>Clear</button>
          </div>
          {popular?.length > 0 && (
            <div className="d-flex flex-wrap gap-2 mb-2">
              {popular.slice(0, 6).map((p) => (
                <button type="button" key={`pop-${p}`} className={`chip ${selected.includes(p) ? 'active' : ''}`} onClick={() => toggleItem(p)}>{p}</button>
              ))}
            </div>
          )}
          <div className="ms-list">
            {filtered.map((o) => (
              <label key={o} className="ms-item">
                <input type="checkbox" className="form-check-input me-2" checked={selected.includes(o)} onChange={() => toggleItem(o)} />
                <span>{o}</span>
              </label>
            ))}
            {filtered.length === 0 && <div className="text-muted small">No matches</div>}
          </div>
        </div>
      )}
    </div>
  );
}