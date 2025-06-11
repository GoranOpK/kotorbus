import https from 'https';
import fs from 'fs';
import httpProxy from 'http-proxy';

const options = {
  key: fs.readFileSync('key.pem'),
  cert: fs.readFileSync('cert.pem')
};

// Laravel backend
const target = 'http://localhost:9000';

// Kreiraj reverse proxy server
const proxy = httpProxy.createProxyServer({ target });

// HTTPS server koji proksira sve ka Laravelu
const server = https.createServer(options, (req, res) => {
  proxy.web(req, res);
});

server.listen(8000, () => {
    console.log('HTTPS server listening on port 8000');
});