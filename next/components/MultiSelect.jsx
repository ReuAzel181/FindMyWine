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

  const summaryText = selected.length
    ? `${selected.slice(0, 3).join(', ')}${selected.length > 3 ? '…' : ''}`
    : 'None';

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