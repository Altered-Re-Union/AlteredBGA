<?php

use PHPUnit\Framework\Attributes\DataProvider;

require_once __DIR__ . '/Replay/Replay.php';
require_once __DIR__ . '/Replay/ReplayRunner.php';

/**
 * Replays every recorded game of replays/*.json and fails at the first request whose
 * notifications differ from the recorded ones.
 */
class ReplayTest extends AlteredTestCase
{
  protected function setUp(): void
  {
    BgaDb::resetDatabase(GAME_DIR);
    Table::harnessResetGlobalsCache();
    Table::$notifications = [];
  }

  public static function replays(): array
  {
    $files = glob(dirname(__DIR__) . '/replays/*.json');
    return array_combine(array_map('basename', $files), array_map(fn($f) => [$f], $files));
  }

  #[DataProvider('replays')]
  public function testReplayMatches(string $file): void
  {
    $replay = new Replay($file);
    $runner = new ReplayRunner($replay, [OPTION_UNDO => 1]);
    $ok = $runner->run(getenv('REPLAY_MAX_MOVES') ? (int) getenv('REPLAY_MAX_MOVES') : null);
    $report = sprintf("%s: %d/%d requests replayed\n", basename($file), count(array_unique(array_column($runner->played, 'move'))), $runner->requestCount())
      . implode("\n", array_map(fn($p) => "  move {$p['move']}: {$p['action']}", array_slice($runner->played, -8)))
      . "\n" . implode("\n", $runner->log);
    @mkdir('/harness/results');
    file_put_contents('/harness/results/replay_' . basename($file, '.json') . '.log', $report . "\n");
    file_put_contents('/harness/results/replay_' . basename($file, '.json') . '.json', json_encode($runner->divergence ? $runner->divergence + ['attempts' => $runner->attempts] : null, JSON_PRETTY_PRINT));
    foreach (BgaDb::$evaluationWrites as $write) {
      file_put_contents('/harness/results/evaluation_writes_replays.txt', basename($file) . ": $write\n", FILE_APPEND);
    }
    $this->assertTrue($ok, "Divergence:\n" . json_encode($runner->divergence, JSON_PRETTY_PRINT) . "\n" . $report);
  }
}
