import express from 'express';
import https from 'https';
import fs from 'fs';
import httpProxy from 'http-proxy';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const options = {
  key: fs.readFileSync(path.join(__dirname, 'key.pem')),
  cert: fs.readFileSync(path.join(__dirname, 'cert.pem'))
};
const proxy = httpProxy.createProxyServer({ target: 'http://localhost:9000' });

// Proxy error handler (VERY IMPORTANT)
proxy.on('error', (err, req, res) => {
  res.writeHead(502, { 'Content-Type': 'text/plain' });
  res.end('Bad gateway: Laravel server is down or unreachable.');
});

// test
app.use('/test', (req, res) => {
  res.end('Node proxy radi!');
});

// Serve static files
app.use(express.static(path.join(__dirname, 'public')));

// Proxy for API
app.use('/api', (req, res) => {
  req.url = '/api' + req.url; // VRATI /api prefix!
  proxy.web(req, res);
});

// Proxy for ADMIN
app.use('/admin', (req, res) => {
  req.url = '/admin' + req.url; // VRATI prefix koji Express uklanja
  proxy.web(req, res);
});

// Test
app.use('/test', (req, res) => {
  res.end('Node proxy radi!');
});

// Serve static files
app.use(express.static(path.join(__dirname, 'public')));

// Everything else (SPA fallback)
app.use((req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

https.createServer(options, app).listen(8000, () => {
  console.log('HTTPS server listening on port 8000');
});