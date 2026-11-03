#!/bin/sh
# Replays every replays/*.json, each in its own PHPUnit process (the game keeps state in
# function-scoped require_once and statics). Reports in results/replay_<table>.log
cd "$(dirname "$0")/.."
docker compose up -d --wait db >/dev/null
pass=0; fail=0
for f in replays/${1:-*}.json; do
  name=$(basename "$f" .json)
  if docker compose run --rm -T php phpunit -c phpunit-replay.xml --filter "$name" >/dev/null 2>&1; then
    pass=$((pass + 1))
  else
    fail=$((fail + 1))
  fi
  head -1 "results/replay_$name.log"
done
echo "$pass passed, $fail failed"
