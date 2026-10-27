# Altered BGA — local test harness

Runs the Altered game server code (PHP) outside Board Game Arena, against a real MariaDB,
with PHPUnit. The proprietary BGA framework is replaced by `framework/module/table/table.game.php`,
which implements only what the game uses: DB access (`APP_DbObject`), the `Table` class
(globals, players, notifications) and a state machine that follows `states.inc.php`.

The game code is used as is: nothing in the game repository needs to change.

## Requirements

Docker (with Compose). On macOS with Colima: `colima start`. Run the commands from `misc/harness/`;
the game code is the repository root (override with `GAME_DIR=/path/to/AlteredBGA`).
`misc/` is not deployed to BGA.

## Running

```bash
docker compose run --rm php phpunit                         # scenario tests + sweep sample (40 cards)
docker compose run --rm php phpunit --filter AngryCrowd     # one test class
docker compose run --rm -e SWEEP=all php phpunit --filter CardSweepTest   # every card, sequential
./sweep.sh                                                  # every card, 6 shards in parallel -> results/
./tools/replay.sh                                           # replays of real games (replays/*.json)
```

## Test kinds

- **Scenario tests** (`AngryCrowdTest`): a real game is created, both players pick a starter deck
  and their mana, then cards are put on the board with `createCard()` and the test plays through
  the same entry points as the client (`actPlay`, `actChooseAction`, `actTarget`...).
- **Card sweep** (`CardSweepTest`): every card is played from hand on a standard board and its
  effect is resolved by a naive player (first choice, first targets, pass optional steps).
  A PHP exception fails the test. States the naive player cannot answer are reported as
  incomplete. The SQL query count per card goes to `results/costs.csv` (or `sweep.csv`),
  which points at slow cards.
- **Cost reports** (`testReport*`): print query counts and timings to `out.log` to compare
  before and after an optimisation.

## Replays of real games

`tests/ReplayTest.php` replays every `replays/*.json` in the local engine and fails at the first
request whose notifications differ from the recorded ones.

```bash
./tools/replay.sh                                           # reports in results/
```

**Replays are not committed**: they hold BGA player names and ids (`replays/*.json` is git-ignored).
Collect them locally (below), or share them privately.

**Getting a replay**: open a finished table on BGA, and in the browser dev tools (Network tab)
copy the JSON response of the request that loads the game logs (`{"status":1,"data":{"logs":[...],"players":[...]}}`).
It must contain the private channels of both players (`/player/p<id>`), which hold the drawn cards.

**Collecting replays in bulk** (`tools/`): BGA's Content-Security-Policy keeps its pages from
calling other hosts, so a logged-in BGA tab hands replays to a local "bridge" page by `postMessage`.

1. `python3 tools/replay_receiver.py` (serves the bridge on 127.0.0.1:8765, writes `replays/<table>.json`).
2. In a logged-in boardgamearena.com tab, open the bridge from a click (popups need a user gesture):
   run `window.__bridge = window.open('http://127.0.0.1:8765/bridge')` from a button or the console.
3. Paste `tools/collect.js` in the console, then `collectReplays({seeds: [<player id>], maxTables: 12})`.
   It lists each player's last 10 Altered tables (`/gamestats/gamestats/getGames.html`), follows their
   opponents, keeps normal ends, and fetches `/archive/archive/logs.html` for each, 4 s apart.
   Archived tables (older than about a day) answer "Cannot find gamenotifs log file" and are skipped.

Then `./tools/replay.sh` replays each file in its own PHPUnit process (`./tools/replay.sh <table>` for
one). Reports: `results/replay_<table>.log` (requests replayed) and `results/replay_<table>.json`
(first divergence, every candidate tried and why it did not match, board state).
`python3 tools/show_move.py <table> <move>` prints the recorded notifications of one request.

**How it works** (`tests/Replay/`): a replay holds the outputs of each request (notifications),
not the inputs, so each request is rebuilt by search. Every action the current state allows is
tried inside a SQL transaction, and the one that produces exactly the recorded notifications and
state sequence is committed. The rest of the setup is read from the replay:

| Missing input | Rebuilt from |
| --- | --- |
| Decks | the API deck sent with `actConfirmAPIDeck` (private args of the deck selection) |
| Deck shuffles | the order in which each card first shows up (the harness intercepts `shuffle()`) |
| Storm layout | the tiles revealed by `moveStormToken` |
| "Undo" preference | players who got a `confirmPartialTurn` have it enabled |
| Token art (`actGetDeckInfos`, Altered API) | the tokens invoked during the game |
| Seat order | the `players` list |
| Dice | the `roll` notifications of the request |
| Cards put back into a deck | before each request, the deck is reordered with the next cards the replay shows leaving it |
| Undone requests (deleted from the log by `Log::revertTo`) | the state the undo lands in: replayed as a no-op, an erased confirmation, or `actRestart` |
| Ids consumed by undone rows | after an undo, the next new card/meeple id in the replay resets the auto-increment |
| Click order of selections | the order ids show up in the notifications, else permutations (up to 4 ids) |
| Order of cards leaving a deck within one request | packets of different channels are not chronological: each interleaving of the channels is tried |
| Requests erased by an undo | short action sequences (up to 3) reaching the state the undo lands in |

**Ambiguity**: a request without game notification (a branch choice, a confirmation) only shows its
state sequence, which several actions can share (e.g. both branches of Angry Crowd lead to "target").
All matching actions are then kept, and the runner snapshots the database and keeps the one after
which the next requests (up to 3) replay too. Erased requests are settled the same way.

Ignored notifications: `gameStateChange` (compared separately as a state sequence),
`updateReflexionTime`, `gameStateMultipleActiveUpdate`, `simpleNode` (framework), and
`newUndoableStep` (deleted from the game log once a turn is confirmed).

**Code version**: a replay is only expected to match the code that was deployed when it was
played. Replaying it after a change that alters behaviour (or even an asset name) diverges, and
that is the point: a refactoring or an optimisation must keep every replay green.

## Profiling

An image with Xdebug's profiler profiles one card of the sweep (setup included):

```bash
docker compose -f docker-compose.yml -f docker-compose.profile.yml run --rm -T \
  -e XDEBUG_TRIGGER=1 -e SWEEP='^LY_Rare_SoulEncounter$' php phpunit -c phpunit-profile.xml
python3 tools/profile_summary.py results/profile/cachegrind.out.<...>   # inclusive and self time per function
```

Profiles are large (about 1 GB). To check that an optimisation keeps behaviour, compare a sweep
against a saved one: copy `results/` to `baseline/` before the change, rerun `./sweep.sh` after, then
`python3 tools/compare_sweep.py baseline results` (outcome of each card, and query and time totals).

## Helpers (`tests/AlteredTestCase.php`)

| Helper | Purpose |
| --- | --- |
| `startGame()` | deck + mana selection for both players, ends on the first `chooseAssignment` |
| `createCard($pId, 'OD_Rare_AngryCrowd', HAND)` | creates a card in any location, returns its id |
| `act('actTarget', [$id])` | engine action as the active player (`actTakeAtomicAction`) |
| `chooseChoice($i)` | picks a branch of an XOR/OR node |
| `as($pId, fn($g) => ...)` | calls a game method as a given player |
| `stateName()`, `args()`, `location($id)`, `cardsOf(...)` | assertions |
| `measure(fn, $profile)` | SQL query count, DB and wall time, optional top queries |

## Fidelity limits

- Framework behaviour is reimplemented from the BGA documentation, not copied: undo savepoints,
  zombie handling, timers and client notifications are not simulated (notifications are only
  recorded in `Table::$notifications`).
- Each action goes through `as()`, which drops static caches and rebuilds the game object, as a new BGA request would. Each test also runs in its own PHP process.
- The JS client (`altered.js`, built on BGA's proprietary `ebg.core.gamegui`) cannot run locally.
