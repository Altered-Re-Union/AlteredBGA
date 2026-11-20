// Collects Altered replays from BGA. Paste in the console of a logged-in boardgamearena.com tab
// after opening the bridge (window.__bridge, see tools/replay_receiver.py), then:
//   collectReplays({seeds: [91926087], maxTables: 12})
// Progress is in window.__collect. Requests are sequential and spaced by `delayMs`.
async function collectReplays({ seeds, maxTables = 12, maxPlayers = 10, delayMs = 4000, gameId = 1909 }) {
  const state = (window.__collect = { done: [], skipped: [], errors: [], requests: 0, running: true });
  const sleep = (ms) => new Promise((r) => setTimeout(r, ms));
  const bridgeOrigin = 'http://127.0.0.1:8765';
  const get = async (url, json = true) => {
    await sleep(delayMs);
    state.requests++;
    const r = await fetch(url, { headers: { 'X-Request-Token': bgaConfig.requestToken }, credentials: 'include' });
    if (!r.ok) throw new Error(`${r.status} on ${new URL(url, location).pathname}`);
    return json ? r.json() : r.text();
  };
  const ask = (message, type) =>
    new Promise((resolve) => {
      const handler = (e) => {
        if (e.origin === bridgeOrigin && e.data && e.data.type === type) {
          removeEventListener('message', handler);
          resolve(e.data);
        }
      };
      addEventListener('message', handler);
      window.__bridge.postMessage(message, bridgeOrigin);
    });

  const known = new Set((await ask({ type: 'list' }, 'list')).tables);
  const players = [...seeds];
  const seenPlayers = new Set(seeds.map(String));
  const tables = [];
  for (let i = 0; i < players.length && i < maxPlayers && tables.length < maxTables * 2; i++) {
    const res = await get(`/gamestats/gamestats/getGames.html?player=${players[i]}&opponent_id=0&game_id=${gameId}&finished=1&updateStats=0`);
    for (const t of (res.data && res.data.tables) || []) {
      for (const p of t.players.split(',')) {
        if (!seenPlayers.has(p)) {
          seenPlayers.add(p);
          players.push(p);
        }
      }
      // Normal ends only: abandons and timeouts end through zombie turns
      if (t.normalend === '1' && t.concede === '0' && !known.has(t.table_id) && !tables.some((x) => x.table_id === t.table_id)) {
        tables.push(t);
      }
    }
  }

  for (const t of tables.slice(0, maxTables)) {
    try {
      // The review page links the replay of the exact game version the table ran on
      const html = await get(`/gamereview?table=${t.table_id}`, false);
      const version = (html.match(/\/archive\/replay\/(\d{6}-\d{4})\//) || [])[1] || null;
      const replay = await get(`/archive/archive/logs.html?table=${t.table_id}&translated=true`);
      if (replay.status != 1) {
        state.skipped.push([t.table_id, replay.error]);
        continue;
      }
      replay.meta = { table: t.table_id, version, start: t.start, end: t.end, players: t.players, ranks: t.ranks };
      const saved = await ask({ type: 'replay', replay }, 'saved');
      state.done.push([t.table_id, version, saved.status]);
    } catch (e) {
      state.errors.push([t.table_id, e.message]);
      if (/^(403|429)/.test(e.message)) break; // throttled: stop
    }
  }
  state.running = false;
  return state;
}
