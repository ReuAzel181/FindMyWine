import { loadWines } from '../../lib/wines';

export default function handler(req, res) {
  try {
    const wines = loadWines();
    res.status(200).json({ wines });
  } catch (err) {
    res.status(500).json({ error: 'Failed to load wines', details: String(err) });
  }
}