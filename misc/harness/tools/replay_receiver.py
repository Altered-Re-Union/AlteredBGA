"""Receives replays collected in a logged-in BGA tab (tools/collect.js) and writes replays/<table>.json.

BGA's Content-Security-Policy forbids its pages to call other hosts, so the BGA tab opens
/bridge (served here) and hands each replay over with postMessage; the bridge POSTs it to
this server, on its own origin. Listens on 127.0.0.1 only.
"""
import json
import os
import re
import sys
from http.server import BaseHTTPRequestHandler, HTTPServer

OUT = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', 'replays')
ORIGIN = 'https://boardgamearena.com'


BRIDGE = """<!doctype html><title>BGA replay bridge</title><pre id=log>waiting for replays</pre><script>
addEventListener('message', async (e) => {
  if (!/^https:\\/\\/([a-z]+\\.)?boardgamearena\\.com$/.test(e.origin)) return;
  if (e.data.type === 'list') {
    const tables = await fetch('/').then(r => r.json());
    e.source.postMessage({type: 'list', tables}, e.origin);
    return;
  }
  const r = await fetch('/', {method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(e.data.replay)});
  document.getElementById('log').textContent += ' | ' +e.data.replay.meta.table + ' ' + r.status;
  e.source.postMessage({type: 'saved', table: e.data.replay.meta.table, status: r.status}, e.origin);
});
</script>"""


class Handler(BaseHTTPRequestHandler):
    def cors(self):
        origin = self.headers.get('Origin', '')
        if origin.endswith('boardgamearena.com'):
            self.send_header('Access-Control-Allow-Origin', origin)
        self.send_header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Content-Type')
        self.send_header('Access-Control-Allow-Private-Network', 'true')

    def do_OPTIONS(self):
        self.send_response(204)
        self.cors()
        self.end_headers()

    def do_GET(self):
        if self.path.startswith('/bridge'):
            self.send_response(200)
            self.send_header('Content-Type', 'text/html')
            self.end_headers()
            self.wfile.write(BRIDGE.encode())
            return
        # Lets the collector skip tables already downloaded
        tables = sorted(f[:-5] for f in os.listdir(OUT) if f.endswith('.json'))
        body = json.dumps(tables).encode()
        self.send_response(200)
        self.cors()
        self.send_header('Content-Type', 'application/json')
        self.end_headers()
        self.wfile.write(body)

    def do_POST(self):
        length = int(self.headers.get('Content-Length', 0))
        payload = json.loads(self.rfile.read(length))
        table = str(payload.get('meta', {}).get('table', ''))
        if not re.fullmatch(r'\d+', table) or payload.get('status') != 1:
            self.send_response(400)
            self.cors()
            self.end_headers()
            return
        with open(os.path.join(OUT, f'{table}.json'), 'w') as f:
            json.dump(payload, f)
        print(f'saved {table} ({length // 1024} kB)', flush=True)
        self.send_response(200)
        self.cors()
        self.end_headers()

    def log_message(self, *args):
        pass


if __name__ == '__main__':
    os.makedirs(OUT, exist_ok=True)
    port = int(sys.argv[1]) if len(sys.argv) > 1 else 8765
    print(f'listening on 127.0.0.1:{port}, writing to {os.path.abspath(OUT)}', flush=True)
    HTTPServer(('127.0.0.1', port), Handler).serve_forever()
