import express from 'express';
import https from 'https';
import fs from 'fs';
import httpProxy from 'http-proxy';
import path from 'path';
import { fileURLToPath } from 'url';
import cookieParser from 'cookie-parser';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const options = {
  key: fs.readFileSync(path.join(__dirname, 'key.pem')),
  cert: fs.readFileSync(path.join(__dirname, 'cert.pem'))
};

// Osnovni Laravel backend
const backendTarget = 'http://localhost:9000';

// Proxy instanca
const proxy = httpProxy.createProxyServer({ target: backendTarget });

proxy.on('error', (err, req, res) => {
  res.writeHead(502, { 'Content-Type': 'text/plain' });
  res.end('Bad gateway: Laravel server is down or unreachable.');
});

// 1. Cookie parser MORA biti prvi!
app.use(cookieParser());

// 2. Proxy za API
app.use('/api', (req, res) => {
  console.log('[PROXY /api]', req.method, req.url);
  // Vrati /api u url, jer Express skida prefix
  if (!req.url.startsWith('/api/')) {
    req.url = '/api' + req.url;
  }
  if (req.cookies && req.cookies['XSRF-TOKEN']) {
    req.headers['x-xsrf-token'] = req.cookies['XSRF-TOKEN'];
  }
  proxy.web(req, res);
});

// 3. Proxy za admin panel (ako koristiš)
app.use('/admin', (req, res) => {
  console.log('[PROXY /admin]', req.method, req.url);
  proxy.web(req, res);
});

// 4. Primer: proxy za /uploads (npr. ako slike servira Laravel)
app.use('/uploads', (req, res) => {
  console.log('[PROXY /uploads]', req.method, req.url);
  proxy.web(req, res);
});

// 5. Primer: proxy za /media (dodaj još po potrebi)
// app.use('/media', (req, res) => {
//   console.log('[PROXY /media]', req.method, req.url);
//   proxy.web(req, res);
// });

// 6. Staticki fajlovi (JS, CSS, img, favicon, ...)
app.use(express.static(path.join(__dirname, 'public')));

// 7. SPA fallback (uvek na kraju!)
app.use((req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

https.createServer(options, app).listen(8000, () => {
  console.log('HTTPS server listening on port 8000');
});