import express from 'express';
import https from 'https';
import fs from 'fs';
import httpProxy from 'http-proxy';
import path from 'path';
import { fileURLToPath } from 'url';
import cookieParser from 'cookie-parser';
import mysql from 'mysql2/promise';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const options = {
  key: fs.readFileSync(path.join(__dirname, 'key.pem')),
  cert: fs.readFileSync(path.join(__dirname, 'cert.pem'))
};

// MySQL konekcija (prilagodi podatke!)
const dbPool = mysql.createPool({
  host: '127.0.0.1',
  user: 'opstinakotor_busnovauser',         // <-- promeni u svoj user
  password: 'Y^1KZbwle%7q3xwq', // <-- promeni u svoju lozinku
  database: 'opstinakotor_busnova'
});

// Laravel backend
const backendTarget = 'http://localhost:9000';

// Proxy instanca
const proxy = httpProxy.createProxyServer({ target: backendTarget });

proxy.on('error', (err, req, res) => {
  res.writeHead(502, { 'Content-Type': 'text/plain' });
  res.end('Bad gateway: Laravel server is down or unreachable.');
});

// 1. Cookie parser MORA biti prvi!
app.use(cookieParser());

// 2. API endpoint za slotove iz MySQL baze
app.get('/api/time_slots', async (req, res) => {
  try {
    const [rows] = await dbPool.query('SELECT time_slot FROM list_of_time_slots ORDER BY id');
    const slots = rows.map(row => row.time_slot);
    res.json(slots);
  } catch (err) {
    console.error('MySQL /api/time_slots error:', err);
    res.status(500).json({ error: 'Database error' });
  }
});

// 3. Proxy za API (sve ostalo što počinje na /api, osim /api/time_slots)
app.use('/api', (req, res, next) => {
  if (req.path.startsWith('/time_slots')) return next(); // preskoči /api/time_slots
  console.log('[PROXY /api]', req.method, req.url);
  if (!req.url.startsWith('/api/')) {
    req.url = '/api' + req.url;
  }
  if (req.cookies && req.cookies['XSRF-TOKEN']) {
    req.headers['x-xsrf-token'] = req.cookies['XSRF-TOKEN'];
  }
  proxy.web(req, res);
});

// 4. Proxy za admin panel (ako koristiš)
app.use('/admin', (req, res) => {
  console.log('[PROXY /admin]', req.method, req.url);
  proxy.web(req, res);
});

// 5. Proxy za /uploads
app.use('/uploads', (req, res) => {
  console.log('[PROXY /uploads]', req.method, req.url);
  proxy.web(req, res);
});

// 6. Proxy za /media (dodaj još po potrebi)
// app.use('/media', (req, res) => {
//   console.log('[PROXY /media]', req.method, req.url);
//   proxy.web(req, res);
// });

// 7. Staticki fajlovi (JS, CSS, img, favicon, ...)
app.use(express.static(path.join(__dirname, 'public')));

// 8. SPA fallback (uvek na kraju!)
app.use((req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

https.createServer(options, app).listen(8000, () => {
  console.log('HTTPS server listening on port 8000');
});